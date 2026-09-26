<?php

namespace App\Filament\Resources\FinancialPeriods\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;


class FinancialPeriodsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('start_date', 'asc')

            // FULL ROW ACTIVE HIGHLIGHT (current quarter)
            ->recordClasses(fn ($record) =>
                now()->between($record->start_date, $record->end_date)
                    ? 'bg-green-200 dark:bg-green-950/60 border-l-4 border-green-600 shadow-sm'
                    : 'hover:bg-gray-50 dark:hover:bg-gray-800'
            )

            ->columns([

                TextColumn::make('financialYear.name')
                    ->label('Financial Year')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quarter')
                    ->label('Quarter')
                    ->formatStateUsing(fn ($state) => "Q{$state}")
                    ->badge()
                    ->color(fn ($record) =>
                        now()->between($record->start_date, $record->end_date)
                            ? 'success'
                            : 'gray'
                    )
                    ->suffix(fn ($record) =>
                        now()->between($record->start_date, $record->end_date)
                            ? ' (Current)'
                            : null
                    )
                    ->sortable(),

                TextColumn::make('label')
                    ->label('Period')
                    ->searchable()
                    ->sortable()
                    ->color(fn ($record) =>
                        now()->between($record->start_date, $record->end_date)
                            ? 'success'
                            : null
                    )
                    ->weight(fn ($record) =>
                        now()->between($record->start_date, $record->end_date)
                            ? 'bold'
                            : 'normal'
                    ),

                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'OPEN' => 'success',
                        'CLOSED' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('closed_at')
                    ->label('Closed At')
                    ->dateTime()
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('financial_year_id')
                    ->label('Financial Year')
                    ->relationship('financialYear', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('quarter')
                    ->options([
                        1 => 'Q1',
                        2 => 'Q2',
                        3 => 'Q3',
                        4 => 'Q4',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'OPEN' => 'Open',
                        'CLOSED' => 'Closed',
                    ]),
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