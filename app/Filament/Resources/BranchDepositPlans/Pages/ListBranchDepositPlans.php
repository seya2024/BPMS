<?php

namespace App\Filament\Resources\BranchDepositPlans\Pages;

use App\Filament\Resources\BranchDepositPlans\BranchDepositPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBranchDepositPlans extends ListRecords
{
    protected static string $resource = BranchDepositPlanResource::class;

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
