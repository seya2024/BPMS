<?php

namespace App\Filament\Resources\AnnualAccountPlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnnualAccountPlansTable
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
                TextColumn::make('annual_target_accounts')
                    ->label('Annual Target')
                    ->sortable(),
                TextColumn::make('q1_target_accounts')
                    ->label('Q1'),
                TextColumn::make('q2_target_accounts')
                    ->label('Q2'),
                TextColumn::make('q3_target_accounts')
                    ->label('Q3'),
                TextColumn::make('q4_target_accounts')
                    ->label('Q4'),
                TextColumn::make('monthly_target_accounts')
                    ->label('Monthly')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('weekly_target_accounts')
                    ->label('Weekly')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('daily_target_accounts')
                    ->label('Daily')
                    ->numeric(2)
                    ->sortable(),
                    
                TextColumn::make('annualPlan.creator.name')
                    ->label('Created By')
                    ->searchable(),
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
