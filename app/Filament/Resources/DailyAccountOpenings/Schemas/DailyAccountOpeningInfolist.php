<?php

namespace App\Filament\Resources\DailyAccountOpenings\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DailyAccountOpeningInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Opening Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('branch.name')
                                    ->label('Branch'),

                                TextEntry::make('business_day')
                                    ->label('Business Day')
                                    ->date(),

                                TextEntry::make('conventional_accounts')
                                    ->label('Conventional'),

                                TextEntry::make('ifb_accounts')
                                    ->label('IFB (Islamic)'),

                                TextEntry::make('target_accounts')
                                    ->label('Target'),

                                TextEntry::make('total')
                                    ->label('Total Actual')
                                    ->badge()
                                    ->color('success'),

                                TextEntry::make('achievement_percent')
                                    ->label('Achievement %')
                                    ->suffix('%')
                                    ->badge()
                                    ->color(fn ($state) => match (true) {
                                        $state >= 100 => 'success',
                                        $state >= 50 => 'warning',
                                        default => 'danger',
                                    }),

                                TextEntry::make('is_target_met')
                                    ->label('Target Met')
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                            ]),

                        TextEntry::make('remarks')
                            ->label('Remarks')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
