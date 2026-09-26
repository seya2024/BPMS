<?php

namespace App\Filament\Resources\FinancialYears\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
class FinancialYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Financial Year')
                    ->placeholder('FY2026/27')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->native(false)
                        ->required()
                        ->rule(function () {
                            return function ($attribute, $value, $fail) {
                                if (! \Carbon\Carbon::parse($value)->isSameDay(
                                    \Carbon\Carbon::parse($value)->month(7)->day(1)
                                )) {
                                    $fail('Financial year must start on July 1.');
                                }
                            };
                        }),

                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->native(false)
                        ->required()
                        ->afterOrEqual('start_date')
                        ->rule(function (Get $get) {
                            return function ($attribute, $value, $fail) use ($get) {

                                $start = \Carbon\Carbon::parse($get('start_date'));
                                $end = \Carbon\Carbon::parse($value);

                                $expectedEnd = $start->copy()->addYear()->subDay();

                                if (! $end->isSameDay($expectedEnd)) {
                                    $fail('Financial year must end on June 30 (1 year cycle).');
                                }
                            };
                        }),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'OPEN' => 'Open',
                        'CLOSING' => 'Closing',
                        'CLOSED' => 'Closed',
                    ])
                    ->default('OPEN')
                    ->required()
                    ->native(false),

                    DateTimePicker::make('opened_at')
                    ->label('Opened At')
                    ->seconds(false)
                    ->default(fn () => now())
                    ->hidden()
                    ->dehydrated(true),

                // DateTimePicker::make('closed_at')
                //     ->label('Closed At')
                //     ->seconds(false),

                // Select::make('closed_by')
                //     ->label('Closed By')
                //     ->relationship('closedBy', 'name')
                //     ->searchable()
                //     ->preload(),
            ]);
    }
}