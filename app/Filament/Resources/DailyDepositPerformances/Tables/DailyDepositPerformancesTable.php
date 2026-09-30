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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class DailyDepositPerformancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('daily_deposit_performances.business_day', 'desc')
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

                TextColumn::make('branch.bankingType.name')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Conventional Banking' => 'primary',
                        'Islamic Banking (IFB)' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(query: fn (Builder $query, string $direction) => $query
                        ->leftJoin('branches', 'daily_deposit_performances.branch_id', '=', 'branches.id')
                        ->leftJoin('banking_types', 'branches.bankingType_id', '=', 'banking_types.id')
                        ->orderBy('banking_types.name', $direction))
                    ->width('140px')
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                // Conventional Banking - Corporate
                TextColumn::make('conventional_corporate')
                    ->label('Conv. Corp')
                    ->money('ETB', locale: 'en')
                    ->alignEnd()
                    ->color('primary')
                    ->width('100px')
                    ->getStateUsing(fn ($record) => $record->details()
                        ->where('banking_type_id', 1)
                        ->where('business_segment_id', 1)
                        ->first()?->amount ?? 0)
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                // Conventional Banking - Retail
                TextColumn::make('conventional_retail')
                    ->label('Conv. Retail')
                    ->money('ETB', locale: 'en')
                    ->alignEnd()
                    ->color('primary')
                    ->width('100px')
                    ->getStateUsing(fn ($record) => $record->details()
                        ->where('banking_type_id', 1)
                        ->where('business_segment_id', 2)
                        ->first()?->amount ?? 0)
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                // Conventional Banking - MSME
                TextColumn::make('conventional_msme')
                    ->label('Conv. MSME')
                    ->money('ETB', locale: 'en')
                    ->alignEnd()
                    ->color('primary')
                    ->width('100px')
                    ->getStateUsing(fn ($record) => $record->details()
                        ->where('banking_type_id', 1)
                        ->where('business_segment_id', 3)
                        ->first()?->amount ?? 0)
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                // Conventional Total
                TextColumn::make('conventional_total')
                    ->label('Conv. Total')
                    ->money('ETB', locale: 'en')
                    ->alignEnd()
                    ->weight('bold')
                    ->color('primary')
                    ->getStateUsing(fn ($record) => $record->details()
                        ->where('banking_type_id', 1)
                        ->sum('amount'))
                    ->width('120px')
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                // IFB - Corporate
                TextColumn::make('ifb_corporate')
                    ->label('IFB Corp')
                    ->money('ETB', locale: 'en')
                    ->alignEnd()
                    ->color('warning')
                    ->width('100px')
                    ->getStateUsing(fn ($record) => $record->details()
                        ->where('banking_type_id', 2)
                        ->where('business_segment_id', 1)
                        ->first()?->amount ?? 0)
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                // IFB - Retail
                TextColumn::make('ifb_retail')
                    ->label('IFB Retail')
                    ->money('ETB', locale: 'en')
                    ->alignEnd()
                    ->color('warning')
                    ->width('100px')
                    ->getStateUsing(fn ($record) => $record->details()
                        ->where('banking_type_id', 2)
                        ->where('business_segment_id', 2)
                        ->first()?->amount ?? 0)
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                // IFB - MSME
                TextColumn::make('ifb_msme')
                    ->label('IFB MSME')
                    ->money('ETB', locale: 'en')
                    ->alignEnd()
                    ->color('warning')
                    ->width('100px')
                    ->getStateUsing(fn ($record) => $record->details()
                        ->where('banking_type_id', 2)
                        ->where('business_segment_id', 3)
                        ->first()?->amount ?? 0)
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                // IFB Total
                TextColumn::make('ifb_total')
                    ->label('IFB Total')
                    ->money('ETB', locale: 'en')
                    ->alignEnd()
                    ->weight('bold')
                    ->color('warning')
                    ->getStateUsing(fn ($record) => $record->details()
                        ->where('banking_type_id', 2)
                        ->sum('amount'))
                    ->width('120px')
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                // Grand Total
                TextColumn::make('total_deposit_amount')
                    ->label('Grand Total')
                    ->money('ETB')
                    ->alignEnd()
                    ->weight('bold')
                    ->color('success')
                    ->sortable()
                    ->width('130px')
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                // Net Change
                TextColumn::make('net_deposit_change')
                    ->label('Net Change')
                   // ->alignEnd()
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
                    })
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                // Net Change %
                TextColumn::make('net_change_percent')
                    ->label('Net Change %')
                    ->alignEnd()
                    ->getStateUsing(function ($record) {
                        $netChange = $record->net_deposit_change ?? 0;
                        $total = $record->total_deposit_amount ?? 0;
                        $previous = $total - $netChange;

                        if ($previous <= 0) {
                            return 'N/A';
                        }

                        $percent = ($netChange / $previous) * 100;
                        $sign = $percent >= 0 ? '+' : '';

                        return $sign . number_format($percent, 2) . '%';
                    })
                    ->formatStateUsing(fn ($state) => $state)
                    ->color(function ($state, $record) {
                        $netChange = $record->net_deposit_change ?? 0;
                        $total = $record->total_deposit_amount ?? 0;
                        $previous = $total - $netChange;

                        if ($previous <= 0) {
                            return 'gray';
                        }

                        $percent = abs($record->net_deposit_change ?? 0) / $previous * 100;

                        return $percent >= 100 ? 'success' : 'danger';
                    })
                    ->width('100px')
                    ->toggleable(isToggledHiddenByDefault: false)
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