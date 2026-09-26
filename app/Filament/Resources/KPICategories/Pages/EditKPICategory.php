<?php

namespace App\Filament\Resources\KPICategories\Pages;

use App\Filament\Resources\KPICategories\KPICategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditKPICategory extends EditRecord
{
    protected static string $resource = KPICategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
