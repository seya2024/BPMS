<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models\BusinessSegment;
use App\Models\DailyDepositPerformance;
use App\Models\District;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use UnitEnum;
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

                                DatePicker::make('business_day')
                                    ->label('Business Day')
                                    ->live()
                                    ->native(false)
                                    ->afterStateUpdated(function (Set $set, mixed $state, Get $get): void {
                                        $set('entries', $this->buildEntries($get('district_id'), $state));
                                    })
                                    ->required()
                                    ->rules([
                                        'required',
                                        'date',
                                        'before:today',
                                    ])
                                    ->validationMessages([
                                        'before' => 'Business day must be yesterday or earlier (today is not allowed).',
                                    ])
                                    ->placeholder('Select date...')
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Conventional Banking')
                    ->description('Enter deposit amounts for Conventional Banking segments. Positive = Increment (Green), Negative = Decrement (Red)')
                    ->columnSpanFull()
                    ->compact()
                    ->schema([
                        $this->buildBankingTypeRepeater('conventional', 'Conventional', $conventionalSegments),
                    ]),

                Section::make('Islamic Banking (IFB)')
                    ->description('Enter deposit amounts for Islamic Banking segments. Positive = Increment (Green), Negative = Decrement (Red)')
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
        $columns = [
            TableColumn::make('Branch')->width('18%'),
        ];

        foreach ($segments as $segment) {
            $columns[] = TableColumn::make($segment['name']) ->markAsRequired()->width('14%');
        }
        $columns[] = TableColumn::make('Total')->width('16%');
        $columns[] = TableColumn::make('Remarks') ->width('10%');
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
                    ->required()
                    ->rules([
                        'required',
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
                    ->extraAttributes(['style' => 'width: 200px; padding: 2px 4px; text-align: right; border: 1px solid #e5e7eb; height: 28px;'])
                    ->inputMode('decimal')
                )->all(),

                TextInput::make('total')
                    ->label('Total')
                    ->disabled()
                    ->dehydrated(false)
                    ->extraAttributes(['style' => 'width: 140px; padding: 2px 4px; text-align: right; font-weight: 600; background: #fef3c7; border: 1px solid #e5e7eb; height: 28px;']),

                TextInput::make('remarks')
                    ->label('Remarks')
                    ->maxLength(500)
                    ->rules(['max:500', 'string'])
                    ->placeholder('Notes...')
                    ->extraAttributes(['style' => 'width: 100%; padding: 2px 4px; border: 1px solid #e5e7eb; height: 28px;']),
            ])
            ->columnSpanFull()
            ->rules([
                'required',
                'array',
                'min:1',
            ])
            ->validationMessages([
                'required' => 'At least one branch entry is required.',
                'min' => 'At least one branch must be entered.',
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

        $existing = blank($businessDay)
            ? collect()
            : DailyDepositPerformance::query()
                ->where('business_day', Carbon::parse($businessDay)->toDateString())
                ->whereIn('branch_id', $branchIds)
                ->get(['branch_id', 'total_deposit_amount', 'new_deposit_amount', 'deposit_inflow_amount', 'deposit_outflow_amount', 'net_deposit_change', 'remarks'])
                ->keyBy('branch_id');

        $conventionalSegments = $this->getSegmentsForBankingType(1);
        $ifbSegments = $this->getSegmentsForBankingType(2);

        // Filter branches by banking type
        $conventionalBranches = $branches->where('bankingType_id', 1);
        $ifbBranches = $branches->where('bankingType_id', 2);

        $conventionalEntries = [];
        $ifbEntries = [];

        foreach ($conventionalBranches as $branch) {
            $record = $existing->get($branch->id);

            // Conventional entries
            $conventionalRow = [
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
            ];
            foreach ($conventionalSegments as $segment) {
                $conventionalRow["segment_{$segment['id']}"] = 0;
            }
            $conventionalRow['total'] = 0;
            $conventionalRow['remarks'] = '';
            $conventionalEntries[] = $conventionalRow;
        }

        foreach ($ifbBranches as $branch) {
            $record = $existing->get($branch->id);

            // IFB entries
            $ifbRow = [
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
            ];
            foreach ($ifbSegments as $segment) {
                $ifbRow["segment_{$segment['id']}"] = 0;
            }
            $ifbRow['total'] = 0;
            $ifbRow['remarks'] = '';
            $ifbEntries[] = $ifbRow;
        }

        return [
            'conventional' => $conventionalEntries,
            'ifb' => $ifbEntries,
        ];
    }

    public function save(): void
    {
        $this->callHook('beforeValidate');

        $data = $this->form->getState();

        $this->callHook('afterValidate');

        $businessDay = Carbon::parse($data['business_day'])->toDateString();
        $conventionalEntries = $data['entries']['conventional'] ?? [];
        $ifbEntries = $data['entries']['ifb'] ?? [];

        if (blank($conventionalEntries) && blank($ifbEntries)) {
            Notification::make()
                ->warning()
                ->title('Nothing to save')
                ->body('Select a district office that has branches, then enter the deposit performance.')
                ->send();

            return;
        }

        $saved = 0;

        DB::transaction(function () use ($conventionalEntries, $ifbEntries, $businessDay, &$saved): void {
            $conventionalSegments = $this->getSegmentsForBankingType(1);
            $ifbSegments = $this->getSegmentsForBankingType(2);

            // Save Conventional entries
            foreach ($conventionalEntries as $entry) {
                if (blank($entry['branch_id'] ?? null)) {
                    continue;
                }

                $total = 0;
                foreach ($conventionalSegments as $segment) {
                    $total += $entry["segment_{$segment['id']}"] ?? 0;
                }

                DailyDepositPerformance::query()->updateOrCreate(
                    [
                        'branch_id' => $entry['branch_id'],
                        'business_day' => $businessDay,
                    ],
                    [
                        'total_deposit_amount' => $total,
                        'remarks' => filled($entry['remarks'] ?? null) ? $entry['remarks'] : null,
                    ],
                );

                // Save detailed segments to daily_deposit_performance_details
                foreach ($conventionalSegments as $segment) {
                    \App\Models\DailyDepositPerformanceDetail::query()->updateOrCreate(
                        [
                            'branch_id' => $entry['branch_id'],
                            'business_day' => $businessDay,
                            'banking_type_id' => 1, // Conventional
                            'business_segment_id' => $segment['id'],
                        ],
                        [
                            'amount' => $entry["segment_{$segment['id']}"] ?? 0,
                            'remarks' => filled($entry['remarks'] ?? null) ? $entry['remarks'] : null,
                        ],
                    );
                }

                $saved++;
            }

            // Save IFB entries
            foreach ($ifbEntries as $entry) {
                if (blank($entry['branch_id'] ?? null)) {
                    continue;
                }

                $total = 0;
                foreach ($ifbSegments as $segment) {
                    $total += $entry["segment_{$segment['id']}"] ?? 0;
                }

                DailyDepositPerformance::query()->updateOrCreate(
                    [
                        'branch_id' => $entry['branch_id'],
                        'business_day' => $businessDay,
                    ],
                    [
                        'total_deposit_amount' => $total,
                        'remarks' => filled($entry['remarks'] ?? null) ? $entry['remarks'] : null,
                    ],
                );

                // Save detailed segments to daily_deposit_performance_details
                foreach ($ifbSegments as $segment) {
                    \App\Models\DailyDepositPerformanceDetail::query()->updateOrCreate(
                        [
                            'branch_id' => $entry['branch_id'],
                            'business_day' => $businessDay,
                            'banking_type_id' => 2, // IFB
                            'business_segment_id' => $segment['id'],
                        ],
                        [
                            'amount' => $entry["segment_{$segment['id']}"] ?? 0,
                            'remarks' => filled($entry['remarks'] ?? null) ? $entry['remarks'] : null,
                        ],
                    );
                }

                $saved++;
            }
        });

        Notification::make()
            ->success()
            ->title('Batch entry saved')
            ->body("{$saved} branch deposit performance" . ($saved === 1 ? '' : 's') . " saved for {$businessDay}.")
            ->send();

        // Reload the rows so the table reflects the persisted values.
        $this->data['entries'] = $this->buildEntries($this->data['district_id'] ?? null, $businessDay);
    }
}