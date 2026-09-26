<?php

namespace App\Filament\Resources\DailyAccountPerformances\Pages;

use App\Filament\Resources\DailyAccountPerformances\DailyAccountPerformanceResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDailyAccountPerformance extends ViewRecord
{
    protected static string $resource = DailyAccountPerformanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
