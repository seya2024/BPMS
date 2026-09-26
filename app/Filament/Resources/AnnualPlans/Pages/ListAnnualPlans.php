<?php

namespace App\Filament\Resources\AnnualPlans\Pages;

use App\Filament\Resources\AnnualPlans\AnnualPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnnualPlans extends ListRecords
{
    protected static string $resource = AnnualPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
        
                CreateAction::make()->createAnother(true)->label('Create Annual Plan')
                ->createAnother(true)
                ->icon('heroicon-o-plus')
                 ->outlined()
                 ->size('sm')
        ];
    }
}
