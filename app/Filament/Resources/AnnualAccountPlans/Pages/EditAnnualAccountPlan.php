<?php

namespace App\Filament\Resources\AnnualAccountPlans\Pages;

use App\Filament\Resources\AnnualAccountPlans\AnnualAccountPlanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAnnualAccountPlan extends EditRecord
{
    protected static string $resource = AnnualAccountPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
