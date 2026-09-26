<?php

namespace App\Filament\Resources\Branches\Schemas;

use App\Filament\Resources\Branches\BranchResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                          TextInput::make('code')->label('Code')->required(),
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

            Select::make('grade')
                ->label('Branch Grade')
                ->options([
                    'I' => 'I',
                    'II' => 'II',
                    'III' => 'III',
                    'IV' => 'IV',
                    'V' => 'V',
                    'VI' => 'VI',
                    'VIII' => 'VIII',
                ])
                ->searchable()
                ->required(),
                // Select::make('parent_id')
                // ->label('Main Branch')
                // ->preload()
                // ->lazy()
                // ->relationship(
                //     name: 'parent',
                //     titleAttribute: 'name',
                //     modifyQueryUsing: fn ($query, $get) => $query->where('id', '!=', $get('id'))
                // )
                // ->searchable()
                // ->preload()
                // ->nullable(),

                // Toggle::make('isClosed')
                //     ->label('Branch Closed')
                //     ->default(false),


                  Select::make('banking_type_id')
                    ->label('Banking Type')
                    ->relationship('bankingType', 'name')
                    ->required()->searchable()->preload(),


                Select::make('district_id')
                    ->label('District Office')
                    ->relationship('district', 'name')
                    ->required()->searchable()->preload(),
            ]);
    }
}
