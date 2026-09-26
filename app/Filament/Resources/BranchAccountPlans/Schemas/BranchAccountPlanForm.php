<?php

namespace App\Filament\Resources\BranchAccountPlans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
//use Filament\Schemas\Components\Utilities\Set;

class BranchAccountPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
        

Select::make('annual_account_plan_id')
    ->label('District - Annual Plan')
    ->options(function () {
        return \App\Models\AnnualAccountPlan::query()
            ->with(['annualPlan.district', 'annualPlan.financialYear'])
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->id => ($item->annualPlan?->district?->name ?? 'N/A')
                        . ' - ' . ($item->annualPlan?->financialYear?->name ?? 'N/A')
                        . ' - ' . $item->annual_target_accounts,
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

        $annualPlanId = $get('annual_account_plan_id');

        if (! $annualPlanId) {
            return [];
        }

        $annualPlan = \App\Models\AnnualAccountPlan::with('annualPlan')
            ->find($annualPlanId);

        $districtId = $annualPlan?->annualPlan?->district_id;

        return \App\Models\Branch::query()
            ->where('district_id', $districtId)
            ->whereNotIn('id', function ($query) use ($annualPlanId) {
                $query->select('branch_id')
                    ->from('branch_account_plans')
                    ->where('annual_account_plan_id', $annualPlanId);
            })
            ->pluck('name', 'id');
    })
    ->disabled(fn (Get $get) => ! $get('annual_account_plan_id'))
    ->rules([
        function (Get $get) {
            return function (string $attribute, $value, $fail) use ($get) {

                $annualPlanId = $get('annual_account_plan_id');

                if (! $annualPlanId || ! $value) {
                    return;
                }

                $exists = \App\Models\BranchAccountPlan::query()
                    ->where('annual_account_plan_id', $annualPlanId)
                    ->where('branch_id', $value)
                    ->exists();

                if ($exists) {
                    $fail('This branch is already assigned to this Annual Plan.');
                }
            };
        }
    ])
    ->required(),

        TextInput::make('annual_target_accounts')
            ->numeric()
            ->minValue(0)
            ->maxValue(fn (Get $get) =>
                \App\Models\AnnualAccountPlan::find($get('annual_account_plan_id'))
                    ?->annual_target_accounts
            )
            ->label('Annual Target'),
            

        TextInput::make('q1_target_accounts')
            ->numeric()
            ->minValue(0)
            ->label('Q1'),

        TextInput::make('q2_target_accounts')
            ->numeric()
            ->minValue(0)
            ->label('Q2'),

        TextInput::make('q3_target_accounts')
            ->numeric()
            ->minValue(0)
            ->label('Q3'),

        TextInput::make('q4_target_accounts')
            ->numeric()
            ->minValue(0)
            ->label('Q4'),

        TextInput::make('monthly_target_accounts')
            ->numeric()
            ->minValue(0)
            ->label('Monthly'),

        TextInput::make('weekly_target_accounts')
            ->numeric()
            ->minValue(0)
            ->label('Weekly'),

        TextInput::make('daily_target_accounts')
            ->numeric()
            ->minValue(0)
            ->label('Daily'),
            ]);
    }
}
