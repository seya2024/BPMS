<?php

namespace App\Filament\Resources\DailyAccountPerformances\Pages;

use App\Filament\Resources\DailyAccountPerformances\DailyAccountPerformanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDailyAccountPerformances extends ListRecords
{
    protected static string $resource = DailyAccountPerformanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
  
                 CreateAction::make()->createAnother(true)->label('Add Daily Account')
                ->createAnother(true)
                ->icon('heroicon-o-plus')
                 ->outlined()
                 ->size('sm')
        ];
    }
}
