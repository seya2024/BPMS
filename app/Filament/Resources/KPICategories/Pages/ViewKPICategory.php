<?php

namespace App\Filament\Resources\KPICategories\Pages;

use App\Filament\Resources\KPICategories\KPICategoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKPICategory extends ViewRecord
{
    protected static string $resource = KPICategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
