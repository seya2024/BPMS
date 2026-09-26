<?php

namespace App\Filament\Resources\BranchDepositPlans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
//use Filament\Schemas\Components\Utilities\Set;


  

class BranchDepositPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
  


         Select::make('annual_deposit_plan_id')
        ->label('District - Annual Deposit Plan')
    ->options(function () {
        return \App\Models\AnnualDepositPlan::query()
            ->with(['annualPlan.district', 'annualPlan.financialYear'])
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->id => ($item->annualPlan?->district?->name ?? 'N/A')
                        . ' - ' . ($item->annualPlan?->financialYear?->name ?? 'N/A')
                        . ' - ' . $item->annual_target_amount,
                ];
            });
    })
    ->live()
    ->afterStateUpdated(fn (callable $set) => $set('branch_id', null))
    ->required(),


            Select::make('branch_id')
                ->label('Branch')
                ->searchable()
                ->options(function (Get $get) {

                    $planId = $get('annual_deposit_plan_id');

                    if (! $planId) {
                        return [];
                    }

        $plan = \App\Models\AnnualDepositPlan::with('annualPlan') ->find($planId);
        $districtId = $plan?->annualPlan?->district_id;
        return \App\Models\Branch::query()
            ->where('district_id', $districtId)
            ->whereNotIn('id', function ($query) use ($planId) {
                $query->select('branch_id')
                    ->from('branch_deposit_plans')
                    ->where('annual_deposit_plan_id', $planId);
            })
            ->pluck('name', 'id');
    })
    ->disabled(fn (Get $get) => ! $get('annual_deposit_plan_id'))
    ->rules([
        function (Get $get) {
            return function ($attribute, $value, $fail) use ($get) {

                $planId = $get('annual_deposit_plan_id');

                if (! $planId || ! $value) {
                    return;
                }

                $exists = \App\Models\BranchDepositPlan::query()
                    ->where('annual_deposit_plan_id', $planId)
                    ->where('branch_id', $value)
                    ->exists();

                if ($exists) {
                    $fail('This branch already has a deposit allocation for this Annual Plan.');
                }
            };
        }
    ])
    ->required(),


        TextInput::make('annual_target_amount')
            ->numeric()
            ->minValue(0)
            ->step(0.01)
            ->maxValue(fn (Get $get) =>
                \App\Models\AnnualDepositPlan::find($get('annual_deposit_plan_id'))
                    ?->annual_target_amount
            )
            ->label('Annual Target Amount'),

        TextInput::make('q1_target_amount')
            ->numeric()
            ->minValue(0)
            ->step(0.01)
            ->label('Q1'),

        TextInput::make('q2_target_amount')
            ->numeric()
            ->minValue(0)
            ->step(0.01)
            ->label('Q2'),

        TextInput::make('q3_target_amount')
            ->numeric()
            ->minValue(0)
            ->step(0.01)
            ->label('Q3'),

        TextInput::make('q4_target_amount')
            ->numeric()
            ->minValue(0)
            ->step(0.01)
            ->label('Q4'),

        TextInput::make('monthly_target_amount')
            ->numeric()
            ->minValue(0)
            ->step(0.01)
            ->label('Monthly'),

        TextInput::make('weekly_target_amount')
            ->numeric()
            ->minValue(0)
            ->step(0.01)
            ->label('Weekly'),

        TextInput::make('daily_target_amount')
            ->numeric()
            ->minValue(0)
            ->step(0.01)
            ->label('Daily'),
            ]);
    }
}
