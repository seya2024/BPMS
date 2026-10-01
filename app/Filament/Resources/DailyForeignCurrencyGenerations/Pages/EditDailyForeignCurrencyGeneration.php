<?php

namespace App\Filament\Resources\DailyForeignCurrencyGenerations\Pages;

use App\Filament\Resources\DailyForeignCurrencyGenerations\DailyForeignCurrencyGenerationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDailyForeignCurrencyGeneration extends EditRecord
{
    protected static string $resource = DailyForeignCurrencyGenerationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}