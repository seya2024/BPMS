<?php

namespace App\Filament\Resources\DailyForeignCurrencyGenerations\Pages;

use App\Filament\Resources\DailyForeignCurrencyGenerations\DailyForeignCurrencyGenerationResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\DailyForeignCurrencyGeneration;

class CreateDailyForeignCurrencyGeneration extends CreateRecord
{
    protected static string $resource = DailyForeignCurrencyGenerationResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        foreach ($data['branches'] as $row) {

            DailyForeignCurrencyGeneration::updateOrCreate(
                [
                    'business_day' => $data['business_day'],
                    'branch_id' => $row['branch_id'],
                ],
                [
                    'amount' => $row['amount'],
                    'target_amount' => $row['target_amount'],
                    'currency_code' => $row['currency_code'],
                    'remarks' => $row['remarks'],
                ]
            );
        }

        // Return any model to satisfy Filament
        return DailyForeignCurrencyGeneration::first();
    }
}