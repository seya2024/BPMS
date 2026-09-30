<?php

namespace App\Filament\Resources\DailyAccountOpenings\Pages;

use App\Filament\Resources\DailyAccountOpenings\DailyAccountOpeningResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDailyAccountOpening extends ViewRecord
{
    protected static string $resource = DailyAccountOpeningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
