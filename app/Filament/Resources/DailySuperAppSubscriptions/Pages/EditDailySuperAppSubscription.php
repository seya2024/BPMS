<?php

namespace App\Filament\Resources\DailySuperAppSubscriptions\Pages;

use App\Filament\Resources\DailySuperAppSubscriptions\DailySuperAppSubscriptionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDailySuperAppSubscription extends EditRecord
{
    protected static string $resource = DailySuperAppSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}