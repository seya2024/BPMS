<?php

namespace App\Filament\Resources\BranchDepositPlans\Pages;

use App\Filament\Resources\BranchDepositPlans\BranchDepositPlanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBranchDepositPlan extends ViewRecord
{
    protected static string $resource = BranchDepositPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
