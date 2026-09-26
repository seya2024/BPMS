<?php

namespace App\Filament\Resources\KPICategories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KPICategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
         TextInput::make('name')
            ->label('Name')
            ->required()
            ->maxLength(255)
            ->unique(ignoreRecord: true),
          

        Textarea::make('description')
            ->label('Description')
            ->maxLength(1000)
            ->nullable()
            ->columnSpanFull(),
            ]);
    }
}
