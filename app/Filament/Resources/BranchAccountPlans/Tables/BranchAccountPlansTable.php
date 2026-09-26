<?php

namespace App\Filament\Resources\BranchAccountPlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;

class BranchAccountPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
  TextColumn::make('annualAccountPlan')
    ->label('District - Annual Account Plan')
    ->formatStateUsing(function ($record) {
        return ($record->annualAccountPlan?->annualPlan?->district?->name ?? 'N/A')
            . ' - ' . ($record->annualAccountPlan?->annualPlan?->financialYear?->name ?? 'N/A')
            . ' - ' . ($record->annualAccountPlan?->annual_target_accounts ?? 0);
    })
    ->sortable()
    ->searchable(),

        TextColumn::make('branch.name')
            ->label('Branch Name')
            ->searchable()
            ->sortable(),

        TextInputColumn::make('annual_target_accounts')
        ->afterStateUpdated(function ($record, $state) {
            logger('Saved value: ' . $state);
        })
            ->label('Branch Annual Target')
            ->sortable(),

         TextInputColumn::make('q1_target_accounts')->type('number')->label('Q1'),
         TextInputColumn::make('q2_target_accounts')->type('number')->label('Q2'),
         TextInputColumn::make('q3_target_accounts')->type('number')->label('Q3'),
         TextInputColumn::make('q4_target_accounts')->type('number')->label('Q4'),

         TextInputColumn::make('monthly_target_accounts')->type('number')->label('Monthly'),
         TextInputColumn::make('weekly_target_accounts')->type('number')->label('Weekly'),
         TextInputColumn::make('daily_target_accounts')->type('number')->label('Daily'),

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
