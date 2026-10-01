<?php

namespace App\Filament\Resources\DailyAccountPerformances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DailyAccountPerformancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('daily_account_performances.business_day', 'desc')
            ->columns([
               TextColumn::make('business_day')
                    ->label('Business Day')
                    ->date()
                    ->sortable(),

                TextColumn::make('branch.name')
                    ->label('Branch')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('branch.district.name')
                    ->label('District')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                TextColumn::make('branch.bankingType.name')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Conventional Banking' => 'primary',
                        'Islamic Banking (IFB)' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(query: fn (Builder $query, string $direction) => $query
                        ->leftJoin('branches', 'daily_account_performances.branch_id', '=', 'branches.id')
                        ->leftJoin('banking_types', 'branches.bankingType_id', '=', 'banking_types.id')
                        ->orderBy('banking_types.name', $direction))
                    ->width('140px')
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                TextColumn::make('total_accounts')
                    ->label('Total')
                    ->numeric()
                    ->sortable()
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                TextColumn::make('active_accounts')
                    ->label('Active')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('new_accounts')
                    ->label('New')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('dormant_accounts')
                    ->label('Dormant')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('reactivated_accounts')
                    ->label('Reactivated')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}


