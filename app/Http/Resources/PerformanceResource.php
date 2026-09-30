<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerformanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'branch' => [
                'id' => $this->branch_id,
                'name' => $this->branch_name,
                'code' => $this->branch_code,
                'district' => $this->district,
            ],
            'deposit' => [
                'target' => $this->deposit_target,
                'actual' => $this->deposit_actual,
                'achievement_percentage' => $this->deposit_achievement,
            ],
            'account' => [
                'target' => $this->account_target,
                'actual' => $this->account_actual,
                'achievement_percentage' => $this->account_achievement,
            ],
        ];
    }
}
