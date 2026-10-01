<?php

namespace App\Filament\Resources\AnnualPlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnnualPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('financialYear.name')
                    ->label('Financial Year')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('district.name')
                    ->label('District')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('deposit')
                    ->label('Deposit')
                    ->numeric(2)
                    ->sortable(),

                TextColumn::make('account')
                    ->label('Accounts')
                    ->sortable(),

                TextColumn::make('super_app_subscriptions')
                    ->label('Super App Subscriptions')
                    ->sortable(),

                TextColumn::make('foreign_currency_target')
                    ->label('Foreign Currency Target')
                    ->numeric(2)
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Created Date')
                    ->dateTime('d M Y')
                    ->sortable(),
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
