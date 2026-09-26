<?php

namespace App\Filament\Resources\Districts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Helpers\FinancialHelper;

class DistrictsTable
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

                TextColumn::make('location_type')
                    ->label('Location Type')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ?? '-'),

                TextColumn::make('branches_count')
                    ->label('Branches')
                    ->counts('branches')
                    ->formatStateUsing(fn ($state, $record) =>
                        is_null($record->location_type) ? '-' : max($state - 0, 0)
                    ),
            ])
            ->recordActions([
                ViewAction::make()
                    ->icon('heroicon-m-bars-4')
                    ->label('Details'),

                EditAction::make(),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}