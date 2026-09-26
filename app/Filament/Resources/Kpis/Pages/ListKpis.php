<?php

namespace App\Filament\Resources\Kpis\Pages;

use App\Filament\Resources\Kpis\KpiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKpis extends ListRecords
{
    protected static string $resource = KpiResource::class;

    protected function getHeaderActions(): array
    {
        return [
                CreateAction::make()->createAnother(true)->label('Add New KPI ')
                ->createAnother(true)
                ->icon('heroicon-o-plus')
                 ->outlined()
                 ->size('sm')
        ];
    }
}
