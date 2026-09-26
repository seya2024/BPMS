<?php

namespace App\Filament\Resources\Kpis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KpisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                       TextColumn::make('name')
                ->label('KPI Name')
                ->searchable()
                ->sortable(),

            TextColumn::make('category.name')
                ->label('Category')
                ->sortable(),

            TextColumn::make('unit')
                ->label('Unit')
                ->badge(),

            TextColumn::make('calculation_method')
                ->label('Calculation Method')
                ->limit(50)
                ->wrap(),

            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])->defaultSort('id', 'desc')
    
        
            
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
