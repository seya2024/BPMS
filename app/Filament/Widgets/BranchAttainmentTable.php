<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\BranchDepositPlan;
use App\Models\DailyDepositPerformance;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class BranchAttainmentTable extends BaseWidget
{
    protected static ?string $heading = 'Branch Attainment Ranking';

    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $from = AnalyticsService::windowStart()->toDateString();
        $to = Carbon::yesterday()->toDateString();

        // Reused by the achievement expression below. MySQL cannot reference a
        // SELECT alias inside the same SELECT list, so the numerator, the daily
        // target and the day count are each repeated as their own subquery.
        $actualSql = 'COALESCE((SELECT SUM(p.total_deposit_amount) FROM daily_deposit_performances p'
            . ' WHERE p.branch_id = branches.id AND DATE(p.business_day) BETWEEN ? AND ?), 0)';

        $targetSql = 'COALESCE((SELECT MAX(bp.daily_target_amount) FROM branch_deposit_plans bp'
            . ' WHERE bp.branch_id = branches.id), 0)';

        $daysSql = '(SELECT COUNT(DISTINCT p2.business_day) FROM daily_deposit_performances p2'
            . ' WHERE p2.branch_id = branches.id AND DATE(p2.business_day) BETWEEN ? AND ?)';

        return $table
            ->query(
                Branch::query()
                    ->select([
                        'branches.id',
                        'branches.name',
                        'branches.district_id',
                        'branches.bankingType_id',
                    ])
                    ->selectSub(
                        DailyDepositPerformance::query()
                            ->selectRaw('COALESCE(SUM(total_deposit_amount), 0)')
                            ->whereColumn('daily_deposit_performances.branch_id', 'branches.id')
                            ->whereDate('business_day', '>=', $from)
                            ->whereDate('business_day', '<=', $to),
                        'deposit_actual'
                    )
                    ->selectSub(
                        DailyDepositPerformance::query()
                            ->selectRaw('COUNT(DISTINCT business_day)')
                            ->whereColumn('daily_deposit_performances.branch_id', 'branches.id')
                            ->whereDate('business_day', '>=', $from)
                            ->whereDate('business_day', '<=', $to),
                        'reported_days'
                    )
                    ->selectSub(
                        BranchDepositPlan::query()
                            ->selectRaw('COALESCE(MAX(daily_target_amount), 0)')
                            ->whereColumn('branch_deposit_plans.branch_id', 'branches.id'),
                        'daily_target'
                    )
                    // Achievable = daily target x days actually reported, so days with
                    // no data are not silently counted as a shortfall.
                    ->selectRaw(
                        '(' . $targetSql . ') * ' . $daysSql . ' as achievable',
                        [$from, $to]
                    )
                    ->selectRaw(
                        'CASE WHEN (' . $targetSql . ') * ' . $daysSql . ' > 0'
                        . ' THEN ROUND((' . $actualSql . ') / ((' . $targetSql . ') * ' . $daysSql . ') * 100, 2)'
                        . ' ELSE NULL END as achievement',
                        // Placeholder order follows the SQL string above:
                        // daysSql (condition), actualSql (numerator), daysSql (denominator).
                        [$from, $to, $from, $to, $from, $to]
                    )
                    ->with(['district', 'bankingType'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Branch')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('district.name')
                    ->label('District')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('bankingType.name')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'IFB' => 'warning',
                        'Conventional' => 'primary',
                        default => 'gray',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('reported_days')
                    ->label('Days')
                    ->alignEnd()
                    ->sortable(),

                Tables\Columns\TextColumn::make('daily_target')
                    ->label('Daily Target')
                    ->formatStateUsing(fn ($state): string => Number::format((float) $state, precision: 2))
                    ->alignEnd()
                    ->sortable(),

                Tables\Columns\TextColumn::make('achievable')
                    ->label('Achievable')
                    ->formatStateUsing(fn ($state): string => Number::format((float) $state, precision: 2))
                    ->alignEnd()
                    ->sortable(),

                Tables\Columns\TextColumn::make('deposit_actual')
                    ->label('Actual')
                    ->formatStateUsing(fn ($state): string => Number::format((float) $state, precision: 2))
                    ->alignEnd()
                    ->sortable(),

                // Variance is a difference of two selected columns, which MySQL cannot
                // compute inside the SELECT list, so it stays PHP-computed and unsortable.
                Tables\Columns\TextColumn::make('variance')
                    ->label('Variance')
                    ->getStateUsing(fn ($record): float => (float) $record->deposit_actual - (float) $record->achievable)
                    ->formatStateUsing(function ($state): string {
                        $value = Number::format(abs((float) $state), precision: 2);

                        return ((float) $state) >= 0 ? "+ {$value}" : "- {$value}";
                    })
                    ->alignEnd()
                    ->color(fn ($state): string => ((float) $state) >= 0 ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('achievement')
                    ->label('Achievement')
                    ->formatStateUsing(fn ($state): string => $state === null ? 'No target' : Number::format((float) $state, precision: 1) . '%')
                    ->alignEnd()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 100 => 'success',
                        $state >= 80 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('district')
                    ->label('District')
                    ->relationship('district', 'name')
                    ->searchable()
                    ->preload()
                    ->indicator('District')
                    ->default(''),
            ])
            ->defaultSort('achievement', 'desc')
            ->paginated([10, 25, 50])
            ->emptyStateHeading('No attainment data')
            ->emptyStateDescription('Record branch performance and set branch targets to see attainment here.');
    }
}
