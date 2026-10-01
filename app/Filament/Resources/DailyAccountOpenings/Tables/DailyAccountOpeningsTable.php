<?php

namespace App\Filament\Resources\DailyAccountOpenings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DailyAccountOpeningsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('branch.name')
                    ->label('Branch')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('branch.district.name')
                    ->label('District')
                    ->toggleable(),

                TextColumn::make('branch.bankingType.name')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Conventional Banking' => 'primary',
                        'Islamic Banking (IFB)' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(query: fn (Builder $query, string $direction) => $query
                        ->leftJoin('branches', 'daily_account_openings.branch_id', '=', 'branches.id')
                        ->leftJoin('banking_types', 'branches.bankingType_id', '=', 'banking_types.id')
                        ->orderBy('banking_types.name', $direction))
                    ->toggleable()
                    ->width('140px')
                    ->extraAttributes(['style' => 'font-size: 12px; padding: 3px 6px;']),

                TextColumn::make('business_day')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextInputColumn::make('conventional_accounts')
                    ->label('Conventional')
                    ->type('number')
                    ->step(1)
                    ->width('80px')
                    ->sortable()
                    ->rules(['required', 'integer', 'min:0'])
                    ->updateStateUsing(function (string $state, $record) {
                        $record->conventional_accounts = (int) $state;
                        $record->save();
                    }),

                TextInputColumn::make('ifb_accounts')
                    ->label('IFB')
                    ->type('number')
                    ->step(1)
                    ->width('80px')
                    ->sortable()
                    ->rules(['required', 'integer', 'min:0'])
                    ->updateStateUsing(function (string $state, $record) {
                        $record->ifb_accounts = (int) $state;
                        $record->save();
                    }),

                TextColumn::make('total')
                    ->label('Total')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextInputColumn::make('target_accounts')
                    ->label('Target')
                    ->type('number')
                    ->step(1)
                    ->width('80px')
                    ->sortable()
                    ->rules(['required', 'integer', 'min:0'])
                    ->updateStateUsing(function (string $state, $record) {
                        $record->target_accounts = (int) $state;
                        $record->save();
                    }),

                TextColumn::make('achievement_percent')
                    ->label('Actual %')
                    ->suffix('%')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 100 => 'success',
                        $state >= 50 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),

                TextInputColumn::make('remarks')
                    ->label('Remarks')
                    ->width('200px')
                    ->placeholder('—')
                    ->updateStateUsing(function (string $state, $record) {
                        $record->remarks = filled($state) ? $state : null;
                        $record->save();
                    }),
            ])
            ->filters([
                SelectFilter::make('district_id')
                    ->label('District')
                    ->relationship('branch.district', 'name')
                    ->searchable()
                    ->preload()
                    ->indicator('District'),

                SelectFilter::make('branch_id')
                    ->label('Branch')
                    ->relationship('branch', 'name', function (Builder $query, HasTable $livewire) {
                        $districtId = $livewire->getTableFilterFormState('district_id')['value'] ?? null;

                        if (filled($districtId)) {
                            $query->where('district_id', $districtId);
                        }

                        return $query;
                    })
                    ->searchable()
                    ->preload()
                    ->visible(fn (HasTable $livewire) => filled($livewire->getTableFilterFormState('district_id')['value'] ?? null)),

                Filter::make('target_met')
                    ->label('Target Met')
                    ->query(fn (Builder $query) => $query->whereRaw('(conventional_accounts + ifb_accounts) >= target_accounts')),

                Filter::make('below_target')
                    ->label('Below Target')
                    ->query(fn (Builder $query) => $query->whereRaw('(conventional_accounts + ifb_accounts) < target_accounts')),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('business_day', 'desc');
    }
}