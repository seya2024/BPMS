<?php

namespace App\Filament\Resources\DailyAccountPerformances\Pages;

use App\Filament\Resources\DailyAccountPerformances\DailyAccountPerformanceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDailyAccountPerformance extends EditRecord
{
    protected static string $resource = DailyAccountPerformanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
