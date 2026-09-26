<?php

namespace App\Filament\Resources\AnnualDepositPlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnnualDepositPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('annualPlan.financialYear.name')
                    ->label('Financial Year')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('annualPlan.district.name')
                    ->label('District')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('annual_target_amount')
                    ->label('Annual Target')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('q1_target_amount')
                    ->label('Q1')
                    ->numeric(2),
                TextColumn::make('q2_target_amount')
                    ->label('Q2')
                    ->numeric(2),
                TextColumn::make('q3_target_amount')
                    ->label('Q3')
                    ->numeric(2),
                TextColumn::make('q4_target_amount')
                    ->label('Q4')
                    ->numeric(2),
                TextColumn::make('monthly_target_amount')
                    ->label('Monthly')
                    ->numeric(2),
                TextColumn::make('weekly_target_amount')
                    ->label('Weekly')
                    ->numeric(2),
                TextColumn::make('daily_target_amount')
                    ->label('Daily')
                    ->numeric(2),
                // TextColumn::make('annualPlan.creator.name')
                //     ->label('Created By')
                //     ->searchable(),
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
