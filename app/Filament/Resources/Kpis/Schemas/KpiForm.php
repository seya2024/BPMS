<?php

namespace App\Filament\Resources\Kpis\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KpiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            TextInput::make('name')
                ->label('KPI Name')
                ->required()
                ->maxLength(255),

            Select::make('category_id')
                ->label('Category')
                ->relationship('category', 'name')
                ->searchable()
                ->preload()
                ->required(),

            Select::make('unit')
                ->label('Unit')
                ->options([
                    '%' => 'Percentage',
                    'count' => 'Count',
                    'amount' => 'Amount',
                    'ratio' => 'Ratio',
                ])
                ->required(),

            Textarea::make('calculation_method')
                ->label('Calculation Method')
                ->rows(3)
                ->nullable()
                ->columnSpanFull(),
            ]);
    }
}
