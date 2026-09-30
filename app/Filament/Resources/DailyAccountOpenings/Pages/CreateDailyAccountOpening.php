<?php

namespace App\Filament\Resources\DailyAccountOpenings\Pages;

use App\Filament\Resources\DailyAccountOpenings\DailyAccountOpeningResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDailyAccountOpening extends CreateRecord
{
    protected static string $resource = DailyAccountOpeningResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['recorded_by'] = auth()->id();
        return $data;
    }
}
