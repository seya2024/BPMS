<?php

namespace App\Filament\Resources\AnnualAccountPlans\Pages;

use App\Filament\Resources\AnnualAccountPlans\AnnualAccountPlanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAnnualAccountPlan extends ViewRecord
{
    protected static string $resource = AnnualAccountPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
