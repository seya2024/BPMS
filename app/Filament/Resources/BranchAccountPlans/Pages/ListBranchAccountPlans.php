<?php

namespace App\Filament\Resources\BranchAccountPlans\Pages;

use App\Filament\Resources\BranchAccountPlans\BranchAccountPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBranchAccountPlans extends ListRecords
{
    protected static string $resource = BranchAccountPlanResource::class;

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
