<?php

namespace App\Filament\Resources\FinancialYears\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Helpers\FinancialHelper;


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

                            foreach ($records as $record) {
                                $record->update([
                                    'status' => 'CLOSED',
                                    'closed_at' => now(),
                                    'closed_by' => auth()->user()->id, // Assuming you have an authenticated user 
                                    'opened_at'  =>  $record->created_at,    	
                                ]);
                            }

                            Notification::make()
                                ->title('Success')
                                ->body('Selected Financial Years have been closed successfully.')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }
}