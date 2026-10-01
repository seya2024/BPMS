<?php

namespace App\Filament\Resources\AnnualPlans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AnnualPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('financial_year_id')
                    ->label('Financial Year')
                    ->relationship('financialYear', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('district_id')
                    ->label('District')
                    ->relationship('district', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('deposit')
                    ->label('Deposit Target')
                    ->numeric()
                    ->prefix('ETB')
                    ->required(),

                TextInput::make('account')
                    ->label('Account Opening Target')
                    ->numeric()
                    ->integer()
                    ->required(),

                TextInput::make('super_app_subscriptions')
                    ->label('Super App Subscription Target')
                    ->numeric()
                    ->integer()
                    ->required(),

                TextInput::make('foreign_currency_target')
                    ->label('Foreign Currency Generation Target')
                    ->numeric()
                    ->prefix('ETB')
                    ->default(0),

                Textarea::make('remarks')
                    ->label('Remarks')
                    ->columnSpanFull()
                    ->rows(3),
            ])
            ->columns(2);
    }
}