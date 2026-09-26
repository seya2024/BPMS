<?php

namespace App\Filament\Resources\AnnualAccountPlans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use App\Models\AnnualPlan;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Services\AccountAllocationService;

class AnnualAccountPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // SELECT PLAN
                Select::make('annual_plan_id')
                    ->label('Annual Plan')
                    ->relationship('annualPlan', 'id')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) =>
                            $record->financialYear->name . ' - ' . $record->district->name
                    ),

                // INPUT + ALLOCATION
                TextInput::make('annual_target_accounts')
                    ->label('Annual Target Accounts')
                    ->numeric()
                    ->required()
                    ->prefix('AC')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {

                        if (blank($state) || blank($get('annual_plan_id'))) {
                            return;
                        }

                        $plan = AnnualPlan::with('financialYear')
                            ->find($get('annual_plan_id'));

                        if (! $plan) {
                            return;
                        }

                        // ✅ FIX: use correct service
                        $targets = AccountAllocationService::allocate(
                            (float) $state,
                            $plan->financialYear->start_date,
                            $plan->financialYear->end_date
                        );

                        $set('q1_target_accounts', $targets['q1_target_accounts'] ?? 0);
                        $set('q2_target_accounts', $targets['q2_target_accounts'] ?? 0);
                        $set('q3_target_accounts', $targets['q3_target_accounts'] ?? 0);
                        $set('q4_target_accounts', $targets['q4_target_accounts'] ?? 0);

                        $set('monthly_target_accounts', $targets['monthly_target_accounts'] ?? 0);
                        $set('weekly_target_accounts', $targets['weekly_target_accounts'] ?? 0);
                        $set('daily_target_accounts', $targets['daily_target_accounts'] ?? 0);
                    }),

                // OUTPUT FIELDS
                TextInput::make('q1_target_accounts')->label('Q1 Target Accounts')->readOnly(),
                TextInput::make('q2_target_accounts')->label('Q2 Target Accounts')->readOnly(),
                TextInput::make('q3_target_accounts')->label('Q3 Target Accounts')->readOnly(),
                TextInput::make('q4_target_accounts')->label('Q4 Target Accounts')->readOnly(),

                TextInput::make('monthly_target_accounts')->label('Monthly Target Accounts')->readOnly(),
                TextInput::make('weekly_target_accounts')->label('Weekly Target Accounts')->readOnly(),
                TextInput::make('daily_target_accounts')->label('Daily Target Accounts')->readOnly(),

                // REMARKS
                Textarea::make('remarks')
                    ->label('Remarks')
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}