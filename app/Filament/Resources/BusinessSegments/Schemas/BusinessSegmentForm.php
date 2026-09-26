<?php

namespace App\Filament\Resources\BusinessSegments\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BusinessSegmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                 TextInput::make('name')
                ->label('Segment Name')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255)
                ->placeholder('e.g. MSME, Corporate, Retail'),

            Textarea::make('description')
                ->label('Description')
                ->rows(3)
                ->maxLength(500)
                ->nullable(),
            ]);
    }
}
