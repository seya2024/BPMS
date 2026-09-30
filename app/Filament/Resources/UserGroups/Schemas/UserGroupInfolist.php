<?php

namespace App\Filament\Resources\UserGroups\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserGroupInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Group Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Group Name'),

                                ColorEntry::make('color')
                                    ->label('Color'),
                            ]),

                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),

                        IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean(),
                    ]),

                Section::make('Permissions')
                    ->schema([
                        RepeatableEntry::make('permissions')
                            ->label('')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Permission')
                                    ->badge(),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Users in this Group')
                    ->schema([
                        RepeatableEntry::make('users')
                            ->label('')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('User Name'),

                                TextEntry::make('email')
                                    ->label('Email'),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
