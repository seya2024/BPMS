<?php

namespace App\Filament\Pages;

use App\Filament\Support\BusinessDay;
use App\Filament\Support\Notify;
use App\Models\Branch;
use App\Models\DailySuperAppSubscription;
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
 * Batch super app subscription entry for one district and one business day.
 *
 * The business day is always in the past: the picker defaults to yesterday and
 * validates `before:today`, matching the other batch pages.
 *
 * Writes go through DailyRecord::upsert() rather than updateOrCreate(); see that
 * class for why the date comparison must be driver-safe.
 */
class BatchDailySuperAppSubscriptions extends Page
{
    protected static ?string $title = 'Batch Super App Subscription Entry';

    protected static ?string $slug = 'daily-super-app-subscriptions/batch';

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
                                    ->options(fn (): array => Cache::remember('batch_entry_districts_superapp', 3600, fn () => District::query()
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
                                    ->afterStateUpdated(function (Set $set, mixed $state, Get $get): void {
                                        $set('entries', $this->buildEntries($get('district_id'), $state));
                                    })
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Super App Subscriptions')
                    ->description('Enter subscription counts for each branch. Positive = Increment, Negative = Decrement.')
                    ->columnSpanFull()
                    ->compact()
                    ->schema([
                        $this->buildSubscriptionRepeater(),
                    ]),
            ]);
    }

    protected function buildSubscriptionRepeater(): Repeater
    {
        $columns = [
            TableColumn::make('Branch')->width('18%'),
            TableColumn::make('Subscriptions')->width('14%'),
            TableColumn::make('Target')->width('14%'),
            TableColumn::make('Total')->width('14%'),
            TableColumn::make('Remarks')->width('10%'),
        ];

        return Repeater::make('entries')
            ->label('Super App Subscriptions')
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

                TextInput::make('subscriptions')
                    ->label('Subscriptions')
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
                    ->inputMode('decimal'),

                TextInput::make('target_subscriptions')
                    ->label('Target')
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
                    ->inputMode('decimal'),

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
                                ->url(fn (): string => \App\Filament\Resources\DailySuperAppSubscriptions\DailySuperAppSubscriptionResource::getUrl('index'))
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
            : DailySuperAppSubscription::query()
                ->where('business_day', Carbon::parse($businessDay)->toDateString())
                ->whereIn('branch_id', $branchIds)
                ->get(['branch_id', 'subscriptions', 'target_subscriptions', 'remarks'])
                ->keyBy('branch_id');

        $entries = [];

        foreach ($branches as $branch) {
            $record = $existing->get($branch->id);

            $entries[] = [
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
                'subscriptions' => (int) ($record->subscriptions ?? 0),
                'target_subscriptions' => (int) ($record->target_subscriptions ?? 0),
                'remarks' => (string) ($record->remarks ?? ''),
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
            Notify::nothingToSave('super app subscriptions');

            return;
        }

        $saved = 0;
        $updated = 0;

        try {
            DB::transaction(function () use ($entries, $businessDay, &$saved, &$updated): void {
                foreach ($entries as $entry) {
                    if (blank($entry['branch_id'] ?? null)) {
                        continue;
                    }

                    $subscriptions = (int) ($entry['subscriptions'] ?? 0);
                    $target = (int) ($entry['target_subscriptions'] ?? 0);
                    $remarks = filled($entry['remarks'] ?? null) ? $entry['remarks'] : null;
                    $branchId = $entry['branch_id'];

                    $row = DailyRecord::upsert(
                        DailySuperAppSubscription::class,
                        $branchId,
                        $businessDay,
                        [
                            'subscriptions' => $subscriptions,
                            'target_subscriptions' => $target,
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
            Notify::saveFailed('super app subscriptions', $e);

            return;
        }

        Notify::batchSaved('super app subscriptions', $saved, $businessDay, updatedCount: $updated);

        // Reload the rows so the table reflects the persisted values.
        $this->data['entries'] = $this->buildEntries($this->data['district_id'] ?? null, $businessDay);
    }
}
