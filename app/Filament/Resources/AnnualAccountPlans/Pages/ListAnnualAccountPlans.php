<?php

namespace App\Filament\Resources\AnnualAccountPlans\Pages;

use App\Filament\Resources\AnnualAccountPlans\AnnualAccountPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnnualAccountPlans extends ListRecords
{
    protected static string $resource = AnnualAccountPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
               CreateAction::make()->createAnother(true)->label('Add Account Plan')
                ->createAnother(true)
                ->icon('heroicon-o-plus')
                 ->outlined()
                 ->size('sm')
        ];
    }
}
