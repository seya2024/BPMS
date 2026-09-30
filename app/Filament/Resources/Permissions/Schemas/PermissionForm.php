<?php

namespace App\Filament\Resources\Permissions\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Guard;

class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Permission Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->columnSpan(1),

                                Select::make('guard_name')
                                    ->label('Guard')
                                    ->options(array_combine(Guard::pluck('name')->toArray(), Guard::pluck('name')->toArray()))
                                    ->default('web')
                                    ->required()
                                    ->columnSpan(1),
                            ]),
                    ]),
            ]);
    }
}
