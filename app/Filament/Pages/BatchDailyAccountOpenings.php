<?php

namespace App\Filament\Pages;

use App\Filament\Support\BusinessDay;
use App\Filament\Support\Notify;
use App\Models\Branch;
use App\Models\DailyAccountOpening;
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
use Throwable;

/**
 * Batch account opening entry for one district and one business day.
 *
 * TWO RULES govern this page and are easy to break. Read before editing.
 *
 * 1. BUSINESS DAY IS ALWAYS IN THE PAST.
 *    The picker defaults to yesterday and validates `before:today`, so a
 *    today-dated row can never be stored.
 *
 * 2. THE CONVENTENTIAL / IFB RULE IS ASYMMETRIC, NOT SYMMETRIC.
 *    A Conventional branch reports BOTH banking types - both columns stay
 *    editable in the Conventional section. An IFB branch has no Conventional
 *    business, so in the IFB section the Conventional column is disabled,
 *    dehydrated away, and stored as SQL NULL. NULL is deliberate:
 *    conventional_accounts is nullable precisely so "not applicable" stays
 *    distinguishable from "reported zero". Writing 0 there would make every IFB
 *    branch look like it had zero Conventional openings.
 *
 *    The disabled field uses ->dehydrated(false) so it never reaches the payload;
 *    save() therefore sets NULL explicitly rather than reading a missing key.
 *
 * Writes go through DailyRecord::upsert() rather than updateOrCreate(); see that
 * class for why the date comparison must be driver-safe.
 */
class BatchDailyAccountOpenings extends Page
{
    protected static ?string $title = 'Batch Account Openings Entry';

    protected static ?string $slug = 'daily-account-openings/batch';

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
        return $schema
            ->columns(12)
            ->components([
                Section::make('Batch Entry')
                    ->description(
                        'Choose a district and a business day, then fill in every branch below and save them all at once.'
                    )
                    ->compact()
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Select::make('district_id')
                                    ->label('District Office')
                                    ->options(
                                        fn (): array => Cache::remember(
                                            'batch_entry_districts',
                                            3600,
                                            fn () => District::query()
                                                ->whereHas('branches')
                                                ->orderBy('name')
                                                ->pluck('name', 'id')
                                                ->all()
                                        )
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(
                                        function (
                                            Set $set,
                                            mixed $state,
                                            Get $get
                                        ): void {
                                            $set(
                                                'entries',
                                                $this->buildEntries(
                                                    $state,
                                                    $get('business_day')
                                                )
                                            );
                                        }
                                    )
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Please select a district office.',
                                    ])
                                    ->placeholder('Select district...')
                                    ->columnSpan(3),

                                BusinessDay::picker()
                                    ->live()
                                    ->afterStateUpdated(
                                        function (
                                            Set $set,
                                            mixed $state,
                                            Get $get
                                        ): void {
                                            $set(
                                                'entries',
                                                $this->buildEntries(
                                                    $get('district_id'),
                                                    $state
                                                )
                                            );
                                        }
                                    )
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Conventional Banking')
                    ->description('Conventional branches report both banking types, so enter Conventional and IFB openings here.')
                    ->columnSpanFull()
                    ->compact()
                    ->schema([
                        $this->buildBankingTypeRepeater('conventional', 'Conventional Banking', 1),
                    ]),

                Section::make('Islamic Banking (IFB)')
                    ->description('IFB branches have no Conventional business, so the Conventional column is disabled and saved as null.')
                    ->columnSpanFull()
                    ->compact()
                    ->schema([
                        $this->buildBankingTypeRepeater('ifb', 'Islamic Banking (IFB)', 2),
                    ]),
            ]);
    }

    /**
     * Builds the per-branch entry table for one banking type section.
     *
     * A Conventional branch reports both banking types, so both columns stay
     * editable in the Conventional section. An IFB branch has no Conventional
     * business, so in the IFB section the Conventional column is disabled and
     * dehydrated away, which makes save() store NULL for it.
     *
     * @param  int  $bankingTypeId  1 = Conventional, 2 = IFB
     */
    protected function buildBankingTypeRepeater(string $key, string $label, int $bankingTypeId): Repeater
    {
        $conventionalApplies = $bankingTypeId === 1;
        return Repeater::make("entries.{$key}")
            ->label($label)
            ->default([])
            ->disableItemCreation()
            ->disableItemDeletion()
            ->disableItemMovement()
            ->collapsible(false)
            ->compact()
            ->table([
                TableColumn::make('Branch')
                    ->width('25%'),

                TableColumn::make('Conventional')
                    ->markAsRequired()
                    ->width('20%'),

                TableColumn::make('IFB (Islamic)')
                    ->markAsRequired()
                    ->width('20%'),

                TableColumn::make('Target')
                    ->markAsRequired()
                    ->width('20%'),

                TableColumn::make('Remarks')
                    ->width('15%'),
            ])
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

                TextInput::make('conventional_accounts')
                    ->label('Conventional')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->maxValue(999999)
                    // IFB branches have no Conventional business: keep the column
                    // visible for a consistent table, but make it inert and drop
                    // it from the submitted payload so save() writes NULL.
                    ->default($conventionalApplies ? 0 : null)
                    ->disabled(! $conventionalApplies)
                    ->dehydrated($conventionalApplies)
                    ->required($conventionalApplies)
                    ->rules($conventionalApplies ? [
                        'required',
                        'integer',
                        'min:0',
                        'max:999999',
                    ] : [])
                    ->validationMessages([
                        'required' => 'Required.',
                        'integer' => 'Must be a whole number.',
                        'min' => 'Cannot be negative.',
                        'max' => 'Max 999,999.',
                    ])
                    ->placeholder($conventionalApplies ? '0' : 'n/a')
                    ->step(1)
                    ->extraAttributes(['style' => 'width: 150px; padding: 2px 4px; text-align: right; border: 1px solid #e5e7eb; height: 28px;'])
                    ->inputMode('numeric'),

                TextInput::make('ifb_accounts')
                    ->label('IFB (Islamic)')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->maxValue(999999)
                    ->default(0)
                    ->required()
                    ->rules([
                        'required',
                        'integer',
                        'min:0',
                        'max:999999',
                    ])
                    ->validationMessages([
                        'required' => 'Required.',
                        'integer' => 'Must be a whole number.',
                        'min' => 'Cannot be negative.',
                        'max' => 'Max 999,999.',
                    ])
                    ->placeholder('0')
                    ->step(1)
                    ->extraAttributes(['style' => 'width: 150px; padding: 2px 4px; text-align: right; border: 1px solid #e5e7eb; height: 28px;'])
                    ->inputMode('numeric'),

                TextInput::make('target_accounts')
                    ->label('Target')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->maxValue(999999)
                    ->default(0)
                    ->required()
                    ->rules([
                        'required',
                        'integer',
                        'min:0',
                        'max:999999',
                    ])
                    ->validationMessages([
                        'required' => 'Required.',
                        'integer' => 'Must be a whole number.',
                        'min' => 'Cannot be negative.',
                        'max' => 'Max 999,999.',
                    ])
                    ->placeholder('0')
                    ->step(1)
                    ->extraAttributes(['style' => 'width: 150px; padding: 2px 4px; text-align: right; border: 1px solid #e5e7eb; height: 28px;'])
                    ->inputMode('numeric'),

                TextInput::make('remarks')
                    ->label('Remarks')
                    ->maxLength(500)
                    ->rules([
                        'max:500',
                        'string',
                    ])
                    ->placeholder('Notes...')
                    // Was 150%, which overflowed the column. 100% matches the
                    // deposit batch page.
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
                                ->url(
                                    fn (): string => \App\Filament\Resources\DailyAccountOpenings\DailyAccountOpeningResource::getUrl(
                                        'index'
                                    )
                                )
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
    protected function buildEntries(
        mixed $districtId,
        mixed $businessDay
    ): array {
        if (blank($districtId)) {
            return ['conventional' => [], 'ifb' => []];
        }

        $branches = Branch::query()
            ->with('bankingType:id,name')
            ->where('district_id', $districtId)
            ->orderBy('bankingType_id')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'bankingType_id',
            ]);

        if ($branches->isEmpty()) {
            return ['conventional' => [], 'ifb' => []];
        }

        $branchIds = $branches->pluck('id');

        $existing = blank($businessDay)
            ? collect()
            : DailyAccountOpening::query()
                ->where(
                    'business_day',
                    Carbon::parse($businessDay)->toDateString()
                )
                ->whereIn('branch_id', $branchIds)
                ->get([
                    'branch_id',
                    'conventional_accounts',
                    'ifb_accounts',
                    'target_accounts',
                    'remarks',
                ])
                ->keyBy('branch_id');

        // Filter branches by banking type
        $conventionalBranches = $branches->where('bankingType_id', 1);
        $ifbBranches = $branches->where('bankingType_id', 2);

        $conventionalEntries = [];
        $ifbEntries = [];

        foreach ($conventionalBranches as $branch) {
            $record = $existing->get($branch->id);

            // Conventional branches report both banking types.
            $conventionalEntries[] = [
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
                'conventional_accounts' => $record?->conventional_accounts ?? 0,
                'ifb_accounts' => $record?->ifb_accounts ?? 0,
                'target_accounts' => $record?->target_accounts ?? 0,
                'remarks' => $record?->remarks ?? '',
            ];
        }

        foreach ($ifbBranches as $branch) {
            $record = $existing->get($branch->id);

            // IFB branches have no Conventional business, so that column is null
            // rather than 0: "not applicable" must not read as "reported zero".
            $ifbEntries[] = [
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
                'conventional_accounts' => null,
                'ifb_accounts' => $record?->ifb_accounts ?? 0,
                'target_accounts' => $record?->target_accounts ?? 0,
                'remarks' => $record?->remarks ?? '',
            ];
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

        $businessDay = Carbon::parse(
            $data['business_day']
        )->toDateString();

        $conventionalEntries = $data['entries']['conventional'] ?? [];
        $ifbEntries = $data['entries']['ifb'] ?? [];

        if (blank($conventionalEntries) && blank($ifbEntries)) {
            Notify::nothingToSave('account openings');

            return;
        }

        $saved = 0;
        $updated = 0;

        try {
            DB::transaction(function () use (
                $conventionalEntries,
                $ifbEntries,
                $businessDay,
                &$saved,
                &$updated
            ): void {
            // Save Conventional entries
            foreach ($conventionalEntries as $entry) {
                if (blank($entry['branch_id'] ?? null)) {
                    continue;
                }

                $row = DailyRecord::upsert(
                    DailyAccountOpening::class,
                    $entry['branch_id'],
                    $businessDay,
                    [
                        // Conventional branches report both banking types.
                        'conventional_accounts' =>
                            (int) ($entry['conventional_accounts'] ?? 0),

                        'ifb_accounts' =>
                            (int) ($entry['ifb_accounts'] ?? 0),

                        'target_accounts' =>
                            (int) ($entry['target_accounts'] ?? 0),

                        'remarks' =>
                            filled($entry['remarks'] ?? null)
                                ? $entry['remarks']
                                : null,

                        'recorded_by' => auth()->id(),
                    ]
                );

                // wasRecentlyCreated comes free from the upsert; a separate
                // exists() check would be one extra query per branch.
                if (! $row->wasRecentlyCreated) {
                    $updated++;
                }

                $saved++;
            }

            // Save IFB entries
            foreach ($ifbEntries as $entry) {
                if (blank($entry['branch_id'] ?? null)) {
                    continue;
                }

                $row = DailyRecord::upsert(
                    DailyAccountOpening::class,
                    $entry['branch_id'],
                    $businessDay,
                    [
                        // An IFB branch has no Conventional business. The form
                        // disables and dehydrates that input away, so the key is
                        // absent here and NULL is stored deliberately.
                        'conventional_accounts' => null,

                        'ifb_accounts' =>
                            (int) ($entry['ifb_accounts'] ?? 0),

                        'target_accounts' =>
                            (int) ($entry['target_accounts'] ?? 0),

                        'remarks' =>
                            filled($entry['remarks'] ?? null)
                                ? $entry['remarks']
                                : null,

                        'recorded_by' => auth()->id(),
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
            Notify::saveFailed('account openings', $e);

            return;
        }

        Notify::batchSaved('account openings', $saved, $businessDay, updatedCount: $updated);

        // Reload the rows so the table reflects the persisted values.
        $this->data['entries'] = $this->buildEntries(
            $this->data['district_id'] ?? null,
            $businessDay
        );
    }
}
