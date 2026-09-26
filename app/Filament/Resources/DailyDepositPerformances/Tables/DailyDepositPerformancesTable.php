<?php

namespace App\Filament\Resources\DailyDepositPerformances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DailyDepositPerformancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('business_day', 'desc')

            ->columns([

                TextColumn::make('business_day')
                    ->label('Business Day')
                    ->date()
                    ->sortable(),

                TextColumn::make('branch.name')
                    ->label('Branch')
                    ->searchable()
                    ->sortable(),

                // TextColumn::make('branch.district.name')
                //     ->label('District')
                //     ->searchable()
                //     ->sortable()
                //     ->toggleable(),

                TextColumn::make('total_deposit_amount')
                    ->label('Total Deposit')
                    ->money('ETB')
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('new_deposit_amount')
                    ->label('New Deposit')
                    ->money('ETB')
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('deposit_inflow_amount')
                    ->label('Inflow')
                    ->money('ETB')
                    ->alignEnd()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('deposit_outflow_amount')
                    ->label('Outflow')
                    ->money('ETB')
                    ->alignEnd()
                    ->color('danger')
                    ->sortable(),


    TextColumn::make('net_deposit_change')
    ->label('Net Change')
    ->alignEnd()
    ->sortable()
    ->formatStateUsing(function ($state) {
        $value = number_format(abs($state), 2);

        if ($state > 0) {
            return "+ {$value}";
        }

        if ($state < 0) {
            return "- {$value}";
        }

        return "0.00";
    })
    ->color(fn ($state) => match (true) {
        $state > 0 => 'success',
        $state < 0 => 'danger',
        default => 'gray',
    }),


                TextColumn::make('remarks')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([

                SelectFilter::make('district')
                    ->relationship('branch.district', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('branch')
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