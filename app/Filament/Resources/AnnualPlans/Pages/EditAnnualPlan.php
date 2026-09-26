<?php

namespace App\Filament\Resources\AnnualPlans\Pages;

use App\Filament\Resources\AnnualPlans\AnnualPlanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAnnualPlan extends EditRecord
{
    protected static string $resource = AnnualPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
