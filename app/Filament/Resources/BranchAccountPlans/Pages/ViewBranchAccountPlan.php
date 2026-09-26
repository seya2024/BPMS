<?php

namespace App\Filament\Resources\BranchAccountPlans\Pages;

use App\Filament\Resources\BranchAccountPlans\BranchAccountPlanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBranchAccountPlan extends ViewRecord
{
    protected static string $resource = BranchAccountPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
