<?php

namespace App\Filament\Resources\UserGroups\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class UserGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Group Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->columnSpan(1),

                                ColorPicker::make('color')
                                    ->default('#3B82F6')
                                    ->columnSpan(1),
                            ]),

                        Textarea::make('description')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->default(true)
                            ->inline(false),
                    ]),

                Section::make('Permissions')
                    ->description('Assign permissions to this group. Users in these group will inherit these permissions.')
                    ->schema([
                        Repeater::make('permissions')
                            ->label('')
                            ->schema([
                                Select::make('name')
                                    ->label('Permission')
                                    ->options(Permission::pluck('name', 'name'))
                                    ->searchable()
                                    ->required()
                                    ->distinct()
                                    ->columnSpanFull(),
                            ])
                            ->addActionLabel('Add Permission')
                            ->reorderable()
                            ->columnSpanFull()
                            ->defaultItems(0),
                    ]),
            ]);
    }
}
