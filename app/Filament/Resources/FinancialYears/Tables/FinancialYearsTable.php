<?php

namespace App\Filament\Resources\FinancialYears\Tables;

use App\Filament\Support\Notify;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FinancialYearsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('start_date', 'desc')

            ->columns([
                TextColumn::make('name')
                    ->label('Financial Year')
                    ->searchable()
                    ->sortable(),




                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'OPEN' => 'success',
                        'CLOSING' => 'warning',
                        'CLOSED' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('opened_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('closed_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('closedBy.name')
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'OPEN' => 'Open',
                        'CLOSING' => 'Closing',
                        'CLOSED' => 'Closed',
                    ]),
            ])

            ->recordActions([
                ViewAction::make(),
                EditAction::make()  ->visible(fn ($record) => $record->status === 'OPEN'),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                     ->disabled(function ($records) {
                            return $records->contains(fn ($record) => $record->status === 'CLOSED');
                        }),

                    Action::make('close_fy')
                        ->label('Close FY')
                        ->icon('heroicon-m-lock-closed')
                        ->color('success')
                        ->requiresConfirmation()
                     ->disabled(function ($records) {
                            return $records->contains(fn ($record) => $record->status === 'CLOSED');
                        })
                          ->accessSelectedRecords() // ✅ REQUIRED FIX
                        ->action(function ($records) {
                            $closed = 0;
                            $skipped = 0;

                            foreach ($records as $record) {
                                // Guard in the loop as well as in ->disabled():
                                // the bulk action should never report a year as
                                // closed when it was left untouched.
                                if ($record->status === 'CLOSED') {
                                    $skipped++;

                                    continue;
                                }

                                $record->update([
                                    'status' => 'CLOSED',
                                    'closed_at' => now(),
                                    'closed_by' => auth()->id(),
                                    'opened_at' => $record->created_at,
                                ]);

                                $closed++;
                            }

                            if ($closed === 0) {
                                Notify::declined(
                                    'Nothing to close',
                                    'Every selected financial year was already closed.'
                                );

                                return;
                            }

                            $body = $closed . ' ' . Notify::plural($closed, 'financial year was', 'financial years were')
                                . ' closed and can no longer be edited.';

                            if ($skipped > 0) {
                                $body .= ' ' . $skipped . ' ' . Notify::plural($skipped, 'year was', 'years were')
                                    . ' already closed and left unchanged.';
                            }

                            Notify::done('Financial years closed', $body);
                        }),
                ]),
            ]);
    }
}
