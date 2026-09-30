<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class BranchPerformanceTable extends BaseWidget
{
    protected static ?string $heading = 'Branch Performance Summary';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $startDate = Carbon::now()->startOfMonth();

        return $table
            ->query(
                Branch::query()
                    ->with(['district'])
                    ->select([
                        'branches.id',
                        'branches.name',
                        'branches.district_id',
                    ])
                    ->selectRaw('(
                        SELECT COALESCE(SUM(total_deposit_amount), 0)
                        FROM daily_deposit_performances
                        WHERE daily_deposit_performances.branch_id = branches.id
                        AND daily_deposit_performances.business_day >= ?
                    ) as deposit_actual', [$startDate])
                    ->selectRaw('(
                        SELECT COALESCE(SUM(total_accounts), 0)
                        FROM daily_account_performances
                        WHERE daily_account_performances.branch_id = branches.id
                        AND daily_account_performances.business_day >= ?
                    ) as account_actual', [$startDate])
                    ->selectRaw('(
                        SELECT COALESCE(MAX(annual_target_amount), 0)
                        FROM branch_deposit_plans
                        WHERE branch_deposit_plans.branch_id = branches.id
                    ) as deposit_target')
                    ->selectRaw('(
                        SELECT COALESCE(MAX(annual_target_accounts), 0)
                        FROM branch_account_plans
                        WHERE branch_account_plans.branch_id = branches.id
                    ) as account_target')
                    ->selectRaw('(
                        SELECT CASE
                            WHEN COALESCE((
                                SELECT MAX(annual_target_amount)
                                FROM branch_deposit_plans
                                WHERE branch_deposit_plans.branch_id = branches.id
                            ), 0) > 0
                            THEN ROUND((
                                COALESCE((
                                    SELECT SUM(total_deposit_amount)
                                    FROM daily_deposit_performances
                                    WHERE daily_deposit_performances.branch_id = branches.id
                                    AND daily_deposit_performances.business_day >= ?
                                ), 0) / (
                                    SELECT MAX(annual_target_amount)
                                    FROM branch_deposit_plans
                                    WHERE branch_deposit_plans.branch_id = branches.id
                                )
                            ) * 100, 2)
                            ELSE 0
                        END
                    ) as deposit_achievement', [$startDate])
                    ->selectRaw('(
                        SELECT CASE
                            WHEN COALESCE((
                                SELECT MAX(annual_target_accounts)
                                FROM branch_account_plans
                                WHERE branch_account_plans.branch_id = branches.id
                            ), 0) > 0
                            THEN ROUND((
                                COALESCE((
                                    SELECT SUM(total_accounts)
                                    FROM daily_account_performances
                                    WHERE daily_account_performances.branch_id = branches.id
                                    AND daily_account_performances.business_day >= ?
                                ), 0) / (
                                    SELECT MAX(annual_target_accounts)
                                    FROM branch_account_plans
                                    WHERE branch_account_plans.branch_id = branches.id
                                )
                            ) * 100, 2)
                            ELSE 0
                        END
                    ) as account_achievement', [$startDate])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Branch Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('district.name')
                    ->label('District')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('deposit_target')
                    ->label('Deposit Target')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 2))
                    ->sortable(),

                Tables\Columns\TextColumn::make('deposit_actual')
                    ->label('Deposit Actual')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 2))
                    ->sortable(),

                Tables\Columns\TextColumn::make('deposit_achievement')
                    ->label('Achievement %')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 1) . '%')
                    ->color(fn ($state): string => match (true) {
                        $state >= 80 => 'success',
                        $state >= 50 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('account_target')
                    ->label('Account Target')
                    ->formatStateUsing(fn ($state): string => number_format((int) $state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('account_actual')
                    ->label('Account Actual')
                    ->formatStateUsing(fn ($state): string => number_format((int) $state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('account_achievement')
                    ->label('Achievement %')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 1) . '%')
                    ->color(fn ($state): string => match (true) {
                        $state >= 80 => 'success',
                        $state >= 50 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),
            ])
            ->defaultSort('name', 'asc');
    }
}
