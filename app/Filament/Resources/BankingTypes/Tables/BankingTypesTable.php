<?php

namespace App\Filament\Resources\BankingTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BankingTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                  TextColumn::make('serial')
                    ->label('#')
                    ->getStateUsing(fn ($record, $column) => $column->getTable()->getRecords()->search($record) + 1)
                    ->sortable(false),

                    TextColumn::make('name')
                    ->label(' Banking Type')
                    ->searchable()
                    ->sortable(),

                    // TextColumn::make('branches_count')
                    //     ->label('Branches')
                    //     ->counts('branches'),

                      

                // TextColumn::make('tag')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true)
                //   ->getStateUsing(fn ($record) => $record->tag ?? '-'),
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
