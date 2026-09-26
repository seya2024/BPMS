<?php

namespace App\Filament\Resources\KPICategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KPICategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('serial')
                    ->label('#')
                    ->state(fn ($record, $rowLoop) => $rowLoop->iteration)
                    ->sortable(false),

                TextColumn::make('name')
                    ->label('Name')
                ->searchable(),
                TextColumn::make('description')
                    ->label('Description')
                  
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
