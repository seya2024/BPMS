<?php

namespace App\Filament\Pages;

use App\Filament\Support\BusinessDay;
use App\Filament\Support\Notify;
use App\Models\Branch;
use App\Models\BusinessSegment;
use App\Models\DailyDepositPerformance;
use App\Models\DailyDepositPerformanceDetail;
use App\Models\District;
use App\Support\DailyRecord;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Throwable;

/**
 * Batch deposit performance entry for one district and one business day.
 *
 * TWO RULES govern this page and are easy to break. Read before editing.
 *
 * 1. BUSINESS DAY IS ALWAYS IN THE PAST.
 *    The picker defaults to yesterday and validates `before:today`. Today is
 *    rejected: the aggregates all read up to yesterday, so a today-dated row
 *    would be invisible in reporting while still being stored.
 *
 * 2. SECTIONS ARE GROUPED BY branches.bankingType_id, AND THEY ARE DISJOINT.
 *    The Conventional section lists only bankingType_id = 1 branches (27) and the
 *    IFB section only bankingType_id = 2 branches (3). A branch therefore appears
 *    in exactly ONE section and writes detail rows for ITS OWN banking type only.
 *    Do not "helpfully" add Conventional branches to the IFB section: the count
 *    would be wrong, and the rows would contradict branches.bankingType_id.
 *
 *    save() still merges entries per branch and replaces only the banking type
 *    being written. That is deliberate belt-and-braces: it stays correct if a
 *    branch ever does appear in both sections, and it guarantees a banking type's
 *    rows are replaced rather than appended to.
 *
 * Writes go through DailyRecord::upsert() rather than updateOrCreate(); see that
 * class for why the date comparison must be driver-safe.
 */
class BatchDailyDepositPerformances extends Page
{
    protected static ?string $title = 'Batch Daily Deposit Performance Entry';

    protected static ?string $slug = 'daily-deposit-performances/batch';

    protected static bool $shouldRegisterNavigation = false;

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'business_day' => now()->subDay()->toDateString(),
            'entries' => [],
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        $conventionalSegments = $this->getSegmentsForBankingType(1); // Conventional
        $ifbSegments = $this->getSegmentsForBankingType(2); // IFB

        return $schema
            ->columns(12)
            ->components([
                Section::make('Batch Entry')
                    ->description('Choose a district and a business day, then fill in every branch below and save them all at once.')
                    ->compact()
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Select::make('district_id')
                                    ->label('District Office')
                                    ->options(fn (): array => Cache::remember('batch_entry_districts_dep', 3600, fn () => District::query()
                                        ->whereHas('branches')
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->all()))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, mixed $state, Get $get): void {
                                        $set('entries', $this->buildEntries($state, $get('business_day')));
                                    })
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Please select a district office.',
                                    ])
                                    ->placeholder('Select district...')
                                    ->columnSpan(3),

                                BusinessDay::picker()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, mixed $state, Get $get): void {
                                        $set('entries', $this->buildEntries($get('district_id'), $state));
                                    })
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Conventional Banking')
                    ->description('Conventional branches only. IFB branches have no Conventional business, so they are not listed here. Positive = Increment (Green), Negative = Decrement (Red)')
                    ->columnSpanFull()
                    ->compact()
                    ->schema([
                        $this->buildBankingTypeRepeater('conventional', 'Conventional', $conventionalSegments),
                    ]),

                Section::make('Islamic Banking (IFB)')
                    ->description('All branches, since Conventional branches also hold IFB deposits. Positive = Increment (Green), Negative = Decrement (Red)')
                    ->columnSpanFull()
                    ->compact()
                    ->schema([
                        $this->buildBankingTypeRepeater('ifb', 'IFB', $ifbSegments),
                    ]),
            ]);
    }

    protected function getSegmentsForBankingType(int $bankingTypeId): array
    {
        return BusinessSegment::whereIn('name', ['Corporate', 'Retail', 'MSME'])
            ->orderByRaw("CASE name WHEN 'Corporate' THEN 1 WHEN 'Retail' THEN 2 WHEN 'MSME' THEN 3 ELSE 4 END")
            ->get(['id', 'name'])
            ->toArray();
    }

    protected function buildBankingTypeRepeater(string $key, string $label, array $segments): Repeater
    {
        // Column widths mirror BatchDailyAccountOpenings so the two batch pages
        // look identical: Branch 25%, data columns share the rest equally,
        // Remarks 15%. The previous 18/14/14/14/16/10 summed to 96% and left a
        // dead gap at the right edge.
        $remarksWidth = 15;
        $branchWidth = 25;
        $dataColumns = count($segments) + 1; // segments + Total
        $dataWidth = (int) floor((100 - $branchWidth - $remarksWidth) / $dataColumns);

        $columns = [
            TableColumn::make('Branch')->width("{$branchWidth}%"),
        ];

        foreach ($segments as $segment) {
            $columns[] = TableColumn::make($segment['name'])->markAsRequired()->width("{$dataWidth}%");
        }

        $columns[] = TableColumn::make('Total')->width("{$dataWidth}%");
        $columns[] = TableColumn::make('Remarks')->width("{$remarksWidth}%");

        return Repeater::make("entries.{$key}")
            ->label($label)
            ->default([])
            ->disableItemCreation()
            ->disableItemDeletion()
            ->disableItemMovement()
            ->collapsible(false)
            ->compact()
            ->table($columns)
            ->extraAttributes([
                'style' => 'border: none; font-family: "SF Mono", "Fira Code", monospace; font-size: 12px; line-height: 1;',
                'class' => 'fi-ta-table-excel',
            ])
            ->schema([
                Hidden::make('branch_id')
                    ->required(),

                TextInput::make('branch_name')
                    ->label('Branch')
                    ->disabled()
                    ->dehydrated(false)
                    ->extraAttributes(['style' => 'font-weight: 600; background: #f3f4f6; border: none; border-right: 1px solid #e5e7eb; padding: 2px 4px; height: 28px;']),

                ...collect($segments)->map(fn ($segment) => TextInput::make("segment_{$segment['id']}")
                    ->label($segment['name'])
                    ->numeric()
                    ->minValue(-9999999999.99)
                    ->maxValue(9999999999.99)
                    ->default(0)
                    ->rules([
                        'nullable',
                        'numeric',
                        'min:-9999999999.99',
                        'max:9999999999.99',
                    ])
                    ->validationMessages([
                        'required' => 'Required.',
                        'numeric' => 'Must be a number.',
                        'min' => 'Min -9,999,999,999.99.',
                        'max' => 'Max 9,999,999,999.99.',
                    ])
                    ->placeholder('0.00')
                    ->step(0.01)
                    // 150px matches the account openings batch page.
                    ->extraAttributes(['style' => 'width: 150px; padding: 2px 4px; text-align: right; border: 1px solid #e5e7eb; height: 28px;'])
                    ->inputMode('decimal')
                )->all(),

                TextInput::make('total')
                    ->label('Total')
                    ->disabled()
                    ->dehydrated(false)
                    ->extraAttributes(['style' => 'width: 150px; padding: 2px 4px; text-align: right; font-weight: 600; background: #fef3c7; border: 1px solid #e5e7eb; height: 28px;']),

                TextInput::make('remarks')
                    ->label('Remarks')
                    ->maxLength(500)
                    ->rules(['max:500', 'string'])
                    ->placeholder('Notes...')
                    ->extraAttributes(['style' => 'width: 100%; padding: 2px 4px; border: 1px solid #e5e7eb; height: 28px;']),
            ])
            ->columnSpanFull()
            ->rules([
                'nullable',
                'array',
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->columnSpanFull()
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('back')
                                ->label('Back')
                                ->icon('heroicon-o-arrow-left')
                                ->url(fn (): string => \App\Filament\Resources\DailyDepositPerformances\DailyDepositPerformanceResource::getUrl('index'))
                                ->color('gray'),
                            Action::make('save')
                                ->label('Save All')
                                ->icon('heroicon-o-check')
                                ->submit('form'),
                        ])
                            ->alignment(Alignment::End)
                            ->fullWidth(),
                    ]),
            ]);
    }

    /**
     * Build the repeater rows for a district + business day.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function buildEntries(mixed $districtId, mixed $businessDay): array
    {
        if (blank($districtId)) {
            return ['conventional' => [], 'ifb' => []];
        }

        $branches = Branch::query()
            ->with('bankingType:id,name')
            ->where('district_id', $districtId)
            ->orderBy('bankingType_id')
            ->orderBy('name')
            ->get(['id', 'name', 'bankingType_id']);

        if ($branches->isEmpty()) {
            return ['conventional' => [], 'ifb' => []];
        }

        $branchIds = $branches->pluck('id');

        $day = blank($businessDay) ? null : Carbon::parse($businessDay)->toDateString();

        $existing = blank($day)
            ? collect()
            : DailyDepositPerformance::query()
                ->where('business_day', $day)
                ->whereIn('branch_id', $branchIds)
                ->get(['branch_id', 'total_deposit_amount', 'new_deposit_amount', 'deposit_inflow_amount', 'deposit_outflow_amount', 'net_deposit_change', 'remarks'])
                ->keyBy('branch_id');

        // Segment amounts keyed by branch_id -> banking_type_id -> segment_id
        $existingDetails = blank($day)
            ? collect()
            : DailyDepositPerformanceDetail::query()
                ->where('business_day', $day)
                ->whereIn('branch_id', $branchIds)
                ->get(['branch_id', 'banking_type_id', 'business_segment_id', 'amount'])
                ->groupBy('branch_id')
                ->map(fn ($branchDetails) => $branchDetails
                    ->groupBy('banking_type_id')
                    ->map(fn ($typeDetails) => $typeDetails->keyBy('business_segment_id')));


        $conventionalSegments = $this->getSegmentsForBankingType(1);
        $ifbSegments = $this->getSegmentsForBankingType(2);

        // Each section lists ONLY the branches whose bankingType_id matches it.
        // An IFB branch (bankingType_id = 2) has no Conventional business, and a
        // Conventional branch (bankingType_id = 1) is not listed under IFB. So
        // the sections are disjoint: 27 Conventional, 3 IFB.
        $conventionalBranches = $branches->where('bankingType_id', 1);
        $ifbBranches = $branches->where('bankingType_id', 2);

        $conventionalEntries = [];
        $ifbEntries = [];

        foreach ($conventionalBranches as $branch) {
            $record = $existing->get($branch->id);
            $details = $existingDetails->get($branch->id, collect())->get(1, collect());

            // Conventional entries
            $conventionalRow = [
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
            ];
            $sectionTotal = 0.0;
            foreach ($conventionalSegments as $segment) {
                $amount = (float) ($details[$segment['id']]->amount ?? 0);
                $conventionalRow["segment_{$segment['id']}"] = $amount;
                $sectionTotal += $amount;
            }
            $conventionalRow['total'] = $sectionTotal;
            // Remarks live on the branch-level record, so they are only editable
            // on the Conventional row to avoid the same text being submitted twice.
            $conventionalRow['remarks'] = (string) ($record->remarks ?? '');
            $conventionalEntries[] = $conventionalRow;
        }

        foreach ($ifbBranches as $branch) {
            $details = $existingDetails->get($branch->id, collect())->get(2, collect());

            // IFB entries
            $ifbRow = [
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
            ];
            $sectionTotal = 0.0;
            foreach ($ifbSegments as $segment) {
                $amount = (float) ($details[$segment['id']]->amount ?? 0);
                $ifbRow["segment_{$segment['id']}"] = $amount;
                $sectionTotal += $amount;
            }
            $ifbRow['total'] = $sectionTotal;
            $ifbRow['remarks'] = '';
            $ifbEntries[] = $ifbRow;
        }

        return [
            'conventional' => $conventionalEntries,
            'ifb' => $ifbEntries,
        ];
    }

    /**
     * Persists every branch row for the selected day.
     *
     * Shape of the write, and why:
     *  - the two repeaters are disjoint (each branch is listed once, under its own
     *    bankingType_id), but entries are still merged per branch so the write
     *    stays correct per banking type if that ever changes;
     *  - each banking type replaces only its OWN detail rows rather than deleting
     *    the branch's rows wholesale (see DailyRecord::replaceDetails);
     *  - the branch-level total is the sum of its detail rows, written once;
     *  - everything runs in one transaction, and any failure is caught so the user
     *    gets a danger notification instead of a 500.
     */
    public function save(): void
    {
        $this->callHook('beforeValidate');

        $data = $this->form->getState();

        $this->callHook('afterValidate');

        $businessDay = Carbon::parse($data['business_day'])->toDateString();
        $conventionalEntries = $data['entries']['conventional'] ?? [];
        $ifbEntries = $data['entries']['ifb'] ?? [];

        if (blank($conventionalEntries) && blank($ifbEntries)) {
            Notify::nothingToSave('deposit performance');

            return;
        }

        $saved = 0;
        $zeroFilled = 0;
        $updated = 0;

        try {
            DB::transaction(function () use ($conventionalEntries, $ifbEntries, $businessDay, &$saved, &$zeroFilled, &$updated): void {
            $conventionalSegments = $this->getSegmentsForBankingType(1);
            $ifbSegments = $this->getSegmentsForBankingType(2);

            // Every branch in the district is stored for the selected business day.
            // A branch left untouched genuinely had no deposit movement that day, so it
            // is recorded as a zero performance rather than being dropped from the report.
            $entries = collect($conventionalEntries)
                ->map(fn ($entry) => ['entry' => $entry, 'bankingTypeId' => 1, 'segments' => $conventionalSegments])
                ->concat(
                    collect($ifbEntries)
                        ->map(fn ($entry) => ['entry' => $entry, 'bankingTypeId' => 2, 'segments' => $ifbSegments])
                )
                ->filter(fn ($row) => filled($row['entry']['branch_id'] ?? null));

            // A Conventional branch appears in both sections, so its rows must be
            // merged per branch: the branch-level total is the sum across banking
            // types, and each banking type only replaces its OWN detail rows.
            // Processing sections independently would overwrite the total with the
            // last section's subtotal and delete the other type's details.
            foreach ($entries->groupBy(fn ($row) => $row['entry']['branch_id']) as $branchRows) {
                $branchId = $branchRows->first()['entry']['branch_id'];

                $total = 0.0;
                $remarks = null;

                foreach ($branchRows as $row) {
                    $entry = $row['entry'];
                    $bankingTypeId = $row['bankingTypeId'];

                    // Replace only this banking type's rows, so the sibling
                    // type's amounts survive.
                    DailyRecord::replaceDetails(
                        DailyDepositPerformanceDetail::class,
                        $branchId,
                        $businessDay,
                        $bankingTypeId,
                        array_map(
                            fn ($segment) => [
                                'business_segment_id' => $segment['id'],
                                'amount' => (float) ($entry["segment_{$segment['id']}"] ?? 0),
                                'remarks' => $entry['remarks'] ?? null,
                            ],
                            $row['segments']
                        )
                    );

                    foreach ($row['segments'] as $segment) {
                        $total += (float) ($entry["segment_{$segment['id']}"] ?? 0);
                    }

                    // Remarks are only editable on the Conventional row.
                    if (blank($remarks) && filled($entry['remarks'] ?? null)) {
                        $remarks = $entry['remarks'];
                    }
                }

                if ($total == 0.0 && blank($remarks)) {
                    $zeroFilled++;
                }

                $row = DailyRecord::upsert(
                    DailyDepositPerformance::class,
                    $branchId,
                    $businessDay,
                    [
                        'total_deposit_amount' => $total,
                        'remarks' => $remarks,
                    ]
                );

                // wasRecentlyCreated comes free from the upsert; a separate
                // exists() check would be one extra query per branch.
                if (! $row->wasRecentlyCreated) {
                    $updated++;
                }

                $saved++;
            }
            });
        } catch (Throwable $e) {
            Notify::saveFailed('deposit performance', $e);

            return;
        }

        if ($saved === 0) {
            Notify::nothingToSave('deposit performance');

            return;
        }

        Notify::batchSaved(
            'deposit performance',
            $saved,
            $businessDay,
            zeroCount: $zeroFilled,
            updatedCount: $updated
        );

        // Reload the rows so the table reflects the persisted values.
        $this->data['entries'] = $this->buildEntries($this->data['district_id'] ?? null, $businessDay);
    }
}
