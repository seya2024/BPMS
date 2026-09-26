<?php

namespace App\Filament\Resources\BranchAccountPlans\Pages;

use App\Filament\Resources\BranchAccountPlans\BranchAccountPlanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBranchAccountPlan extends EditRecord
{
    protected static string $resource = BranchAccountPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
