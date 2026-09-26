<?php

namespace App\Filament\Resources\DailyDepositPerformances\Pages;

use App\Filament\Resources\DailyDepositPerformances\DailyDepositPerformanceResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\DailyDepositPerformance;

class CreateDailyDepositPerformance extends CreateRecord
{
    protected static string $resource = DailyDepositPerformanceResource::class;


    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
{
    foreach ($data['branches'] as $row) {

        DailyDepositPerformance::updateOrCreate(
            [
                'business_day' => $data['business_day'],
                'branch_id' => $row['branch_id'],
            ],
            [
                'total_deposit_amount' => $row['total_deposit_amount'],
                'new_deposit_amount' => $row['new_deposit_amount'],
                'deposit_inflow_amount' => $row['deposit_inflow_amount'],
                'deposit_outflow_amount' => $row['deposit_outflow_amount'],
                'net_deposit_change' => $row['deposit_inflow_amount'] - $row['deposit_outflow_amount'],
                'remarks' => $row['remarks'],
            ]
        );
    }

    // Return any model to satisfy Filament
    return DailyDepositPerformance::first();
}
}
