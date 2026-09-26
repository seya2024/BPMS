<?php

namespace App\Filament\Resources\BranchDepositPlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;

class BranchDepositPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
     


     TextColumn::make('annualDepositPlan')
    ->label('District - Annual Deposit Plan')
    ->formatStateUsing(function ($record) {
        return ($record->annualDepositPlan?->annualPlan?->district?->name ?? 'N/A')
            . ' - ' . ($record->annualDepositPlan?->annualPlan?->financialYear?->name ?? 'N/A')
            . ' - ' . ($record->annualDepositPlan?->annual_target_amount ?? 0);
    })
    ->sortable()
    ->searchable(),



        TextColumn::make('branch.name')
            ->label('Branch')
            ->searchable()
            ->sortable(),

        TextColumn::make('annual_target_amount')
            ->label('Annual target')
            ->money('ETB')
            ->sortable(),


  

         TextColumn::make('q1_target_amount')->label('Q1')->money('ETB'),
         TextColumn::make('q2_target_amount')->label('Q2')->money('ETB'),
         TextColumn::make('q3_target_amount')->label('Q3')->money('ETB'),
         TextColumn::make('q4_target_amount')->label('Q4')->money('ETB'),

        TextColumn::make('monthly_target_amount')->label('Monthly')->money('ETB'),
        TextColumn::make('weekly_target_amount')->label('Weekly')->money('ETB'),
        TextColumn::make('daily_target_amount')->label('Daily')->money('ETB'),
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
