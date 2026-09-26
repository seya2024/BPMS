<?php

namespace App\Filament\Resources\DailyAccountPerformances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DailyAccountPerformancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
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
                    ->sortable(),

                TextColumn::make('total_accounts')
                    ->label('Total')
                    ->numeric()
                    ->sortable(),

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


