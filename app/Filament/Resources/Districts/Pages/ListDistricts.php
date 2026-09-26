<?php

namespace App\Filament\Resources\Districts\Pages;

use App\Filament\Resources\Districts\DistrictResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDistricts extends ListRecords
{
    protected static string $resource = DistrictResource::class;

    protected function getHeaderActions(): array
    {
        return [
          
               CreateAction::make()->createAnother(true)->label('Add New District')
                ->createAnother(true)
                ->icon('heroicon-o-plus')
                 ->outlined()
                 ->size('sm')
        ];
    }
}
