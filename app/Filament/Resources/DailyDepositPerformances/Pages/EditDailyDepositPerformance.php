<?php

namespace App\Filament\Resources\DailyDepositPerformances\Pages;

use App\Filament\Resources\DailyDepositPerformances\DailyDepositPerformanceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDailyDepositPerformance extends EditRecord
{
    protected static string $resource = DailyDepositPerformanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
