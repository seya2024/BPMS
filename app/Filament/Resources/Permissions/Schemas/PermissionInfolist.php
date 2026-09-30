<?php

namespace App\Filament\Resources\Permissions\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PermissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Permission Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Permission Name'),

                                TextEntry::make('guard_name')
                                    ->label('Guard'),

                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->label('Updated At')
                                    ->dateTime(),
                            ]),
                    ]),

                Section::make('Assigned Groups')
                    ->description('Groups that have this permission')
                    ->schema([
                        RepeatableEntry::make('groups')
                            ->label('')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Group Name')
                                    ->badge(),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
