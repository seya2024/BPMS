<?php

namespace App\Filament\Resources\DailyAccountOpenings\Pages;

use App\Filament\Resources\DailyAccountOpenings\DailyAccountOpeningResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDailyAccountOpening extends EditRecord
{
    protected static string $resource = DailyAccountOpeningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
