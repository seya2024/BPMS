<?php

namespace App\Filament\Resources\DailyDepositPerformances\Pages;

use App\Filament\Resources\DailyDepositPerformances\DailyDepositPerformanceResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDailyDepositPerformance extends ViewRecord
{
    protected static string $resource = DailyDepositPerformanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
