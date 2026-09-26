<?php

namespace App\Filament\Resources\KPICategories\Pages;

use App\Filament\Resources\KPICategories\KPICategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKPICategories extends ListRecords
{
    protected static string $resource = KPICategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
           

             CreateAction::make()->createAnother(true)->label('Add KPI Category')
                ->createAnother(true)
                ->icon('heroicon-o-plus')
                 ->outlined()
                 ->size('sm')
        ];
    }
}
