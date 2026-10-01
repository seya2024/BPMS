<?php

namespace App\Filament\Resources\DailyForeignCurrencyGenerations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class DailyForeignCurrencyGenerationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('daily_foreign_currency_generations.business_day', 'desc')
            ->recordUrl(null)
            ->paginated([10, 25, 50])
            ->striped()
            ->columns([
                TextColumn::make('business_day')
                    ->label('Business Day')
                    ->date()
                    ->sortable()
                    ->weight('bold')
                    ->width('120px')
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                TextColumn::make('branch.name')
                    ->label('Branch')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->width('180px')
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                TextColumn::make('branch.district.name')
                    ->label('District')
                    ->searchable()
                    ->sortable()
                    ->width('150px')
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                TextColumn::make('branch.bankingType.name')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Conventional Banking' => 'primary',
                        'Islamic Banking (IFB)' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(query: fn (Builder $query, string $direction) => $query
                        ->leftJoin('branches', 'daily_foreign_currency_generations.branch_id', '=', 'branches.id')
                        ->leftJoin('banking_types', 'branches.bankingType_id', '=', 'banking_types.id')
                        ->orderBy('banking_types.name', $direction))
                    ->width('140px')
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('ETB')
                    ->sortable()
                    ->alignEnd()
                    ->width('120px')
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                TextColumn::make('target_amount')
                    ->label('Target')
                    ->money('ETB')
                    ->sortable()
                    ->alignEnd()
                    ->width('120px')
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                TextColumn::make('currency_code')
                    ->label('Currency')
                    ->badge()
                    ->color('info')
                    ->width('80px')
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                TextColumn::make('achievement')
                    ->label('Achievement %')
                    ->suffix('%')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 100 => 'success',
                        $state >= 50 => 'warning',
                        default => 'danger',
                    })
                    ->sortable()
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                TextColumn::make('remarks')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),
            ])
            ->filters([
                SelectFilter::make('district')
                    ->label('District')
                    ->relationship('branch.district', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('branch')
                    ->label('Branch')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('business_day')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['from'],
                                fn ($query, $date) => $query->whereDate('business_day', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn ($query, $date) => $query->whereDate('business_day', '<=', $date),
                            );
                    }),
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