<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models\DailyAccountPerformance;
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

class BatchDailyAccountPerformances extends Page
{
    protected static ?string $title = 'Batch Daily Account Performance Entry';

    protected static ?string $slug = 'daily-account-performances/batch';

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
                    ->description('Choose a district and a business day, then fill in every branch below and save them all at once.')
                    ->compact()
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Select::make('district_id')
                                    ->label('District Office')
                                    ->options(fn (): array => Cache::remember('batch_entry_districts_perf', 3600, fn () => District::query()
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
                                        'before_or_equal:yesterday',
                                    ])
                                    ->validationMessages([
                                        'before_or_equal' => 'Business day must be yesterday or earlier.',
                                    ])
                                    ->placeholder('Select date...')
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Branches')
                    ->description(fn (Get $get): string => filled($get('district_id'))
                        ? 'Enter the day\'s account performance. Existing entries for this day are pre-filled.'
                        : 'Select a district to load its branches.')
                    ->columnSpanFull()
                    ->compact()
                    ->schema([
                        Repeater::make('entries')
                            ->label('Branch Performance')
                            ->default([])
                            ->disableItemCreation()
                            ->disableItemDeletion()
                            ->disableItemMovement()
                            ->collapsible(false)
                            ->compact()
                            ->table([
                                TableColumn::make('Branch')
                                    ->width('15%'),
                                TableColumn::make('Total')
                                    ->markAsRequired()
                                    ->width('12%'),
                                TableColumn::make('Active')
                                    ->markAsRequired()
                                    ->width('12%'),
                                TableColumn::make('New')
                                    ->markAsRequired()
                                    ->width('12%'),
                                TableColumn::make('Dormant')
                                    ->markAsRequired()
                                    ->width('12%'),
                                TableColumn::make('Reactivated')
                                    ->markAsRequired()
                                    ->width('12%'),
                                TableColumn::make('Remarks')
                                    ->width('19%'),
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

                                TextInput::make('total_accounts')
                                    ->label('Total')
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

                                TextInput::make('active_accounts')
                                    ->label('Active')
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

                                TextInput::make('new_accounts')
                                    ->label('New')
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

                                TextInput::make('dormant_accounts')
                                    ->label('Dormant')
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

                                TextInput::make('reactivated_accounts')
                                    ->label('Reactivated')
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
                            ]),
                    ]),
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
                                ->url(fn (): string => \App\Filament\Resources\DailyAccountPerformances\DailyAccountPerformanceResource::getUrl('index'))
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
            return [];
        }

        $branches = Branch::query()
            ->with('bankingType:id,name')
            ->where('district_id', $districtId)
            ->orderBy('bankingType_id')
            ->orderBy('name')
            ->get(['id', 'name', 'bankingType_id']);

        if ($branches->isEmpty()) {
            return [];
        }

        $branchIds = $branches->pluck('id');

        $existing = blank($businessDay)
            ? collect()
            : DailyAccountPerformance::query()
                ->where('business_day', Carbon::parse($businessDay)->toDateString())
                ->whereIn('branch_id', $branchIds)
                ->get(['branch_id', 'total_accounts', 'active_accounts', 'new_accounts', 'dormant_accounts', 'reactivated_accounts', 'remarks'])
                ->keyBy('branch_id');

        $entries = [];

        foreach ($branches as $branch) {
            $record = $existing->get($branch->id);

            $entries[] = [
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
                'total_accounts' => $record?->total_accounts ?? 0,
                'active_accounts' => $record?->active_accounts ?? 0,
                'new_accounts' => $record?->new_accounts ?? 0,
                'dormant_accounts' => $record?->dormant_accounts ?? 0,
                'reactivated_accounts' => $record?->reactivated_accounts ?? 0,
                'remarks' => $record?->remarks ?? '',
            ];
        }

        return $entries;
    }

    public function save(): void
    {
        $this->callHook('beforeValidate');

        $data = $this->form->getState();

        $this->callHook('afterValidate');

        $businessDay = Carbon::parse($data['business_day'])->toDateString();
        $entries = $data['entries'] ?? [];

        if (blank($entries)) {
            Notification::make()
                ->warning()
                ->title('Nothing to save')
                ->body('Select a district office that has branches, then enter the performance.')
                ->send();

            return;
        }

        $saved = 0;

        DB::transaction(function () use ($entries, $businessDay, &$saved): void {
            foreach ($entries as $entry) {
                if (blank($entry['branch_id'] ?? null)) {
                    continue;
                }

                DailyAccountPerformance::query()->updateOrCreate(
                    [
                        'branch_id' => $entry['branch_id'],
                        'business_day' => $businessDay,
                    ],
                    [
                        'total_accounts' => (int) ($entry['total_accounts'] ?? 0),
                        'active_accounts' => (int) ($entry['active_accounts'] ?? 0),
                        'new_accounts' => (int) ($entry['new_accounts'] ?? 0),
                        'dormant_accounts' => (int) ($entry['dormant_accounts'] ?? 0),
                        'reactivated_accounts' => (int) ($entry['reactivated_accounts'] ?? 0),
                        'remarks' => filled($entry['remarks'] ?? null) ? $entry['remarks'] : null,
                    ],
                );

                $saved++;
            }
        });

        Notification::make()
            ->success()
            ->title('Batch entry saved')
            ->body("{$saved} branch performance" . ($saved === 1 ? '' : 's') . " saved for {$businessDay}.")
            ->send();

        // Reload the rows so the table reflects the persisted values.
        $this->data['entries'] = $this->buildEntries($this->data['district_id'] ?? null, $businessDay);
    }
}