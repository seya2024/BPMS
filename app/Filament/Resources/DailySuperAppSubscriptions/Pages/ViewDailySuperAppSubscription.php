<?php

namespace App\Filament\Resources\DailySuperAppSubscriptions\Pages;

use App\Filament\Resources\DailySuperAppSubscriptions\DailySuperAppSubscriptionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDailySuperAppSubscription extends ViewRecord
{
    protected static string $resource = DailySuperAppSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}