<?php

namespace App\Filament\Resources\AnnualDepositPlans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use App\Models\AnnualPlan;
use App\Services\TargetAllocationService;
//use Filament\Forms\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class AnnualDepositPlanForm
{
   public static function configure(Schema $schema): Schema
{
    return $schema
        ->components([

            Select::make('annual_plan_id')
                ->label('Annual Plan')
                ->relationship('annualPlan', 'id')
                ->searchable()
                ->preload()
                ->live()
                ->required()
                 ->unique(table: 'annual_deposit_plans', column: 'annual_plan_id', ignoreRecord: true)
                ->getOptionLabelFromRecordUsing(
                    fn ($record) =>
                        $record->financialYear->name . ' - ' . $record->district->name
                )
                ->afterStateUpdated(function ($state, Get $get, Set $set) {

                    if (blank($state) || blank($get('annual_target_amount'))) {
                        return;
                    }

                    $annualPlan = AnnualPlan::with('financialYear')->find($state);

                    if (! $annualPlan) {
                        return;
                    }
                 
                    $targets = TargetAllocationService::allocate(
                        (float) $get('annual_target_amount'),
                        $annualPlan->financialYear->start_date,
                        $annualPlan->financialYear->end_date
                    );

                    foreach ($targets as $field => $value) {
                        $set($field, $value);
                    }
                }),

            TextInput::make('annual_target_amount')
                ->label('Annual Target Amount')
                ->numeric()
                ->prefix('ETB')
                ->required()
                ->live(onBlur: true)
           
                ->afterStateUpdated(function ($state, Get $get, Set $set) {

                    if (blank($state) || blank($get('annual_pPlan_id'))) {
                        return;
                    }

                    $annualPlan = AnnualPlan::with('financialYear')
                        ->find($get('annual_plan_id'));

                    if (! $annualPlan) {
                        return;
                    }
                  
                    $targets = TargetAllocationService::allocate(
                        (float) $state,
                        $annualPlan->financialYear->start_date,
                        $annualPlan->financialYear->end_date
                    );

                    foreach ($targets as $field => $value) {
                        $set($field, $value);
                    }
                }),



            
                 // READ ONLY UI FIELDS
                TextInput::make('q1_target_amount')
                    ->label('Q1 Target Amount')
                    ->readOnly(),

                TextInput::make('q2_target_amount')
                    ->label('Q2 Target Amount')
                    ->readOnly(),

                TextInput::make('q3_target_amount')
                    ->label('Q3 Target Amount')
                    ->readOnly(),

                TextInput::make('q4_target_amount')
                    ->label('Q4 Target Amount')
                    ->readOnly(),

                TextInput::make('monthly_target_amount')
                    ->label('Monthly Target Amount')
                    ->readOnly(),

                TextInput::make('weekly_target_amount')
                    ->label('Weekly Target Amount')
                    ->readOnly(),

                TextInput::make('daily_target_amount')
                    ->label('Daily Target Amount')
                    ->readOnly(),


            Textarea::make('remarks')
                ->label('Remarks')
                ->rows(3)
                ->columnSpanFull(),
        ])
        ->columns(2);
}
}