<?php

namespace App\Filament\Resources\AnnualDepositPlans\Pages;

use App\Filament\Resources\AnnualDepositPlans\AnnualDepositPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnnualDepositPlans extends ListRecords
{
    protected static string $resource = AnnualDepositPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
        
              CreateAction::make()->createAnother(true)->label('Add Deposit Plan')
                ->createAnother(true)
                ->icon('heroicon-o-plus')
                 ->outlined()
                 ->size('sm')
        ];
    }
}
