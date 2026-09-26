<?php

namespace App\Filament\Resources\DailyDepositPerformances\Pages;

use App\Filament\Resources\DailyDepositPerformances\DailyDepositPerformanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDailyDepositPerformances extends ListRecords
{
    protected static string $resource = DailyDepositPerformanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
          

               CreateAction::make()->createAnother(true)->label('Add Daily Deposit')
                ->createAnother(true)
                ->icon('heroicon-o-plus')
                 ->outlined()
                 ->size('sm')
        ];
    }
}
