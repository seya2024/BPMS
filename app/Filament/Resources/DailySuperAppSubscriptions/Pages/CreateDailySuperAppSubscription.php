<?php

namespace App\Filament\Resources\DailySuperAppSubscriptions\Pages;

use App\Filament\Resources\DailySuperAppSubscriptions\DailySuperAppSubscriptionResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\DailySuperAppSubscription;

class CreateDailySuperAppSubscription extends CreateRecord
{
    protected static string $resource = DailySuperAppSubscriptionResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        foreach ($data['branches'] as $row) {

            DailySuperAppSubscription::updateOrCreate(
                [
                    'business_day' => $data['business_day'],
                    'branch_id' => $row['branch_id'],
                ],
                [
                    'subscriptions' => $row['subscriptions'],
                    'target_subscriptions' => $row['target_subscriptions'],
                    'remarks' => $row['remarks'],
                ]
            );
        }

        // Return any model to satisfy Filament
        return DailySuperAppSubscription::first();
    }
}