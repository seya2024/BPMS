<?php

namespace App\Filament\Resources\FinancialPeriods\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FinancialPeriodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
            Select::make('financial_year_id')
            ->label('Financial Year')
            ->relationship(
                name: 'financialYear',
                titleAttribute: 'name',
                modifyQueryUsing: fn ($query) => $query->where('status', 'OPEN')
            )
            ->searchable()
            ->preload()
            ->required(),

         Select::make('quarter')
            ->label('Quarter')
            ->options([
                1 => 'Q1',
                2 => 'Q2',
                3 => 'Q3',
                4 => 'Q4',
            ])
            ->required()
            ->rules([
                function (callable $get) {
                    return function ($attribute, $value, $fail) use ($get) {
                        $exists = \App\Models\FinancialPeriod::where('financial_year_id', $get('financial_year_id'))
                            ->where('quarter', $value)
                            ->exists();

                        if ($exists) {
                            $fail('This quarter already exists for the selected financial year.');
                        }
                    };
                }
            ]),

                TextInput::make('label')
                    ->label('Period Label')
                    ->placeholder('Q1 FY2026/27')
                    ->required()
                    ->maxLength(255),

                DatePicker::make('start_date')
                    ->label('Start Date')
                    ->native(false)
                    ->required(),

                DatePicker::make('end_date')
                    ->label('End Date')
                    ->native(false)
                    ->afterOrEqual('start_date')
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'OPEN' => 'Open',
                        'CLOSED' => 'Closed',
                    ])
                    ->default('OPEN')
                    ->required()
                    ->native(false),

                // DatePicker::make('closed_at')
                //     ->label('Closed Date')
                //     ->native(false),
            ]);
    }
}