<?php

namespace App\Filament\Resources\Branches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Helpers\FinancialHelper;

class BranchesTable
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
                    ->label('Branch / Outlet')
                    ->formatStateUsing(fn ($state, $record) => "{$record->name} ({$record->code})")
                    ->searchable()
                    ->sortable(),

                TextColumn::make('grade')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(fn ($record) => $record->grade ?? '-'),
                      

                // TextColumn::make('tag')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true)
                //   ->getStateUsing(fn ($record) => $record->tag ?? '-'),
                TextColumn::make('district.name')
                    ->label('District')->sortable()->searchable()
                      ->getStateUsing(fn ($record) => $record->district?->name ?? '-'),


                 TextColumn::make('bankingType.name')
                    ->label('Banking Type')->sortable()->searchable()
                      ->getStateUsing(fn ($record) => $record->bankingType?->name ?? '-'),

                // ToggleColumn::make('isClosed')
                //     ->label('Branch status')
                //     ->onIcon('heroicon-o-lock-closed')
                //     ->offIcon('heroicon-o-lock-open')
                //     ->onColor('danger')
                //     ->offColor('success')
                //     ->sortable()
                //     ->toggleable(),
                  
            ]) ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('district')
                ->label('Filter by district office')
                ->relationship('district', 'name'),
            ])
            ->recordActions([
                ViewAction::make()->label('Details')->icon('heroicon-m-bars-4')->label('Details'),
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
