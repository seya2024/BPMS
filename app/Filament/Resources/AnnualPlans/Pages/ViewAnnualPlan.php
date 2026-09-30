<?php

namespace App\Filament\Resources\AnnualPlans\Pages;

use App\Filament\Actions\ApprovePlanAction;
use App\Filament\Actions\RejectPlanAction;
use App\Filament\Actions\SubmitForApprovalAction;
use App\Filament\Resources\AnnualPlans\AnnualPlanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAnnualPlan extends ViewRecord
{
    protected static string $resource = AnnualPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            SubmitForApprovalAction::make(),
            ApprovePlanAction::make(),
            RejectPlanAction::make(),
        ];
    }
}
