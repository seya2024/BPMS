<?php

namespace App\Filament\Resources\DailyAccountPerformances\Pages;

use App\Filament\Pages\BatchDailyAccountPerformances;
use App\Filament\Resources\DailyAccountPerformances\DailyAccountPerformanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDailyAccountPerformances extends ListRecords
{
    protected static string $resource = DailyAccountPerformanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(true)
                ->label('Add Daily Account')
                ->icon('heroicon-o-plus')
                ->outlined()
                ->size('sm'),

            Actions\Action::make('batchEntry')
                ->label('Batch Entry')
                ->icon('heroicon-o-square-3-stack-3d')
                ->color('gray')
                ->url(fn (): string => BatchDailyAccountPerformances::getUrl(panel: 'admin')),
        ];
    }
}