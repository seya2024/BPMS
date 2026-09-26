<?php

namespace App\Filament\Resources\AnnualDepositPlans\Pages;

use App\Filament\Resources\AnnualDepositPlans\AnnualDepositPlanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAnnualDepositPlan extends ViewRecord
{
    protected static string $resource = AnnualDepositPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
