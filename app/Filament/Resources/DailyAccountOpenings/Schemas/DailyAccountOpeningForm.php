<?php

namespace App\Filament\Resources\DailyAccountOpenings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DailyAccountOpeningForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Opening Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('branch_id')
                                    ->label('Branch')
                                    ->relationship('branch', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(1),

                                DatePicker::make('business_day')
                                    ->label('Business Day')
                                    ->required()
                                    ->default(now())
                                    ->columnSpan(1),

                                TextInput::make('conventional_accounts')
                                    ->label('Conventional')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->required()
                                    ->columnSpan(1),

                                TextInput::make('ifb_accounts')
                                    ->label('IFB (Islamic)')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->required()
                                    ->columnSpan(1),

                                TextInput::make('target_accounts')
                                    ->label('Target')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->required()
                                    ->columnSpan(1),
                            ]),

                        Textarea::make('remarks')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
