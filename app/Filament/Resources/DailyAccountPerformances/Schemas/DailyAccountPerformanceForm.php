<?php

namespace App\Filament\Resources\DailyAccountPerformances\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DailyAccountPerformanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                DatePicker::make('business_day')
                    ->label('Business Day')
                    ->native(false)
                    ->required(),

                Select::make('branch_id')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('total_accounts')
                    ->label('Total Accounts')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->required(),

                TextInput::make('active_accounts')
                    ->label('Active Accounts')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->required(),

                TextInput::make('new_accounts')
                    ->label('New Accounts')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->required(),

                TextInput::make('dormant_accounts')
                    ->label('Dormant Accounts')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->required(),

                TextInput::make('reactivated_accounts')
                    ->label('Reactivated Accounts')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->required(),

                Textarea::make('remarks')
                    ->label('Remarks')
                    ->rows(3)
                    ->columnSpanFull(),

            ])
            ->columns(2);
    }
}