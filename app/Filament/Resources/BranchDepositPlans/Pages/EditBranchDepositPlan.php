<?php

namespace App\Filament\Resources\BranchDepositPlans\Pages;

use App\Filament\Resources\BranchDepositPlans\BranchDepositPlanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBranchDepositPlan extends EditRecord
{
    protected static string $resource = BranchDepositPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
