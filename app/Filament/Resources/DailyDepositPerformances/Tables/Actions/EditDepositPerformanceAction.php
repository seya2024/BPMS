<?php

namespace App\Filament\Resources\DailyDepositPerformances\Tables\Actions;

use App\Filament\Support\BusinessDay;
use App\Models\Branch;
use App\Models\BusinessSegment;
use App\Models\DailyDepositPerformance;
use App\Models\DailyDepositPerformanceDetail;
use App\Support\DailyRecord;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Row-level edit for one branch/day of deposit performance.
 *
 * IMPORTANT: this modal edits ONE banking type's Corporate/Retail/MSME figures,
 * not the whole branch. A Conventional branch holds rows for both banking type 1
 * and 2, and the IFB leg is owned by the batch page. So persist():
 *  - replaces only the edited banking type's segment rows;
 *  - recomputes the branch total as the sum of ALL detail rows, so the IFB leg
 *    this modal cannot see is still counted;
 *  - deliberately does NOT delete every banking type's rows, which would
 *    silently discard the sibling leg.
 */
class EditDepositPerformanceAction
{
    /**
     * Segment IDs are seeded as MSME=1, Retail=2, Corporate=3, so they are always
     * resolved by name rather than hardcoded.
     */
    public static function segmentId(string $name): int
    {
        return (int) (BusinessSegment::where('name', $name)->value('id') ?? 0);
    }

    public static function make(): EditAction
    {
        return EditAction::make()
            ->label('Edit')
            ->icon('heroicon-o-pencil-square')
            ->outlined()
            ->size('sm')
            ->modalWidth('lg')
            ->modalHeading('Edit Deposit Performance')
            ->modalDescription('Update the segment amounts for this branch and business day.')
            ->modalSubmitActionLabel('Save')
            ->fillForm(fn (DailyDepositPerformance $record): array => self::fill($record))
            ->schema(fn (DailyDepositPerformance $record): array => self::schema($record))
            // using() runs inside process(), which is the only place Filament
            // injects the validated form data as $data. Overriding ->action()
            // instead replaces the default handler, and an `array $data`
            // parameter cannot be auto-injected there, so save() never ran.
            ->using(fn (array $data, DailyDepositPerformance $record) => self::persist($data, $record));
    }

    protected static function fill(DailyDepositPerformance $record): array
    {
        $bankingTypeId = $record->branch?->bankingType_id ?? 1;

        // whereDate() is required: a bare date string does not match the stored
        // "YYYY-MM-DD 00:00:00" value on every driver.
        $details = DailyDepositPerformanceDetail::query()
            ->where('branch_id', $record->branch_id)
            ->whereDate('business_day', $record->business_day?->toDateString())
            ->where('banking_type_id', $bankingTypeId)
            ->get()
            ->keyBy('business_segment_id');

        $amount = fn (string $segmentName): float => (float) ($details[self::segmentId($segmentName)]->amount ?? 0);

        return [
            'branch_id' => $record->branch_id,
            'business_day' => $record->business_day?->toDateString(),
            'corporate' => $amount('Corporate'),
            'retail' => $amount('Retail'),
            'msme' => $amount('MSME'),
            'remarks' => $record->remarks,
        ];
    }

    protected static function schema(DailyDepositPerformance $record): array
    {
        $bankingTypeId = $record->branch?->bankingType_id ?? 1;
        $isIfb = $bankingTypeId === 2;
        $label = $isIfb ? 'Islamic Banking (IFB)' : 'Conventional Banking';

        return [
            Section::make('Branch')
                ->compact()
                ->schema([
                    Select::make('branch_id')
                        ->label('Branch')
                        ->options(fn () => Branch::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->preload()
                        ->required()
                        ->default($record->branch_id),

                    BusinessDay::picker()
                        // Editing keeps the row's own day, clamped to yesterday so a
                        // mis-dated row cannot pre-fill a forbidden date.
                        ->default(
                            $record->business_day
                                ? Carbon::parse($record->business_day)
                                    ->min(Carbon::yesterday())
                                    ->toDateString()
                                : null
                        ),
                ])
                ->columns(2),

            Section::make($label . ' Segments')
                ->description('Positive = Increment, negative = Decrement.')
                ->compact()
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextInput::make('corporate')
                                ->label('Corporate')
                                ->numeric()
                                ->default(0)
                                ->rules(['nullable', 'numeric'])
                                ->extraAttributes(['style' => 'text-align: right; font-family: monospace;']),

                            TextInput::make('retail')
                                ->label('Retail')
                                ->numeric()
                                ->default(0)
                                ->rules(['nullable', 'numeric'])
                                ->extraAttributes(['style' => 'text-align: right; font-family: monospace;']),

                            TextInput::make('msme')
                                ->label('MSME')
                                ->numeric()
                                ->default(0)
                                ->rules(['nullable', 'numeric'])
                                ->extraAttributes(['style' => 'text-align: right; font-family: monospace;']),
                        ]),
                ]),

            Section::make('Remarks')
                ->compact()
                ->schema([
                    Textarea::make('remarks')
                        ->label('Remarks')
                        ->rows(2)
                        ->maxLength(500),
                ]),
        ];
    }

    /**
     * Persists an edited deposit performance row together with its segment
     * details.
     *
     * Public and side-effect-isolated so it can be exercised directly; using()
     * below is only the thin adapter that hands it the validated form data.
     */
    public static function persist(array $data, DailyDepositPerformance $record): void
    {
        $branchId = $data['branch_id'] ?? $record->branch_id;
        $businessDay = $data['business_day'] ?? $record->business_day?->toDateString();

        // This modal edits ONE banking type's segment figures. A Conventional
        // branch also holds IFB rows, which the batch page owns, so the branch
        // total is recomputed from all detail rows below rather than from the
        // three fields in this form.
        $bankingTypeId = Branch::query()->whereKey($branchId)->value('bankingType_id') ?? 1;

        $amounts = [
            self::segmentId('Corporate') => (float) ($data['corporate'] ?? 0),
            self::segmentId('Retail') => (float) ($data['retail'] ?? 0),
            self::segmentId('MSME') => (float) ($data['msme'] ?? 0),
        ];

        $remarks = filled($data['remarks'] ?? null) ? $data['remarks'] : null;

        DB::transaction(function () use ($record, $branchId, $businessDay, $bankingTypeId, $amounts, $remarks): void {
            // Replace only this banking type's segment rows. A Conventional branch
            // also holds IFB rows, and wiping every type here would discard them.
            $rows = [];

            foreach ($amounts as $segmentId => $amount) {
                if ($segmentId <= 0) {
                    continue;
                }

                $rows[] = [
                    'business_segment_id' => $segmentId,
                    'amount' => $amount,
                    'remarks' => $remarks,
                ];
            }

            DailyRecord::replaceDetails(
                DailyDepositPerformanceDetail::class,
                $branchId,
                $businessDay,
                $bankingTypeId,
                $rows
            );

            // The branch-level total is the sum of ALL its detail rows across
            // banking types, so a Conventional branch's total keeps counting the
            // IFB amounts that this modal does not edit.
            $grandTotal = DailyRecord::detailTotal(
                DailyDepositPerformanceDetail::class,
                $branchId,
                $businessDay
            );

            DailyRecord::upsert(
                DailyDepositPerformance::class,
                $branchId,
                $businessDay,
                [
                    'total_deposit_amount' => $grandTotal,
                    'remarks' => $remarks,
                ]
            );

            $record->refresh();
        });
    }
}
