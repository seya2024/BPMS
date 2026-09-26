<?php

namespace App\Filament\Resources\Districts\Schemas;

use App\Filament\Resources\Branches\BranchResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DistrictForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            
                TextInput::make('name')
                ->label('Name')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->rule([
                    'required',
                    'string',
                    'regex:/^[A-Za-z ]+$/',
                ]),
                Select::make('location_type')
                    ->label('Location Type')
                    ->options([
                        'City' => 'City',
                        'Upcountry' => 'Upcountry',
                    ])
                    ->required()
         
            ]);
    }
}
