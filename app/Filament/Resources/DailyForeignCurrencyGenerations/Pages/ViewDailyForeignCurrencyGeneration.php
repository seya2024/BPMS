<?php

namespace App\Filament\Resources\DailyForeignCurrencyGenerations\Pages;

use App\Filament\Resources\DailyForeignCurrencyGenerations\DailyForeignCurrencyGenerationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDailyForeignCurrencyGeneration extends ViewRecord
{
    protected static string $resource = DailyForeignCurrencyGenerationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}