<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnualPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'financial_year' => $this->whenLoaded('financialYear', function () {
                return [
                    'id' => $this->financialYear->id,
                    'name' => $this->financialYear->name,
                ];
            }),
            'district' => $this->whenLoaded('district', function () {
                return [
                    'id' => $this->district->id,
                    'name' => $this->district->name,
                ];
            }),
            'deposit' => $this->deposit,
            'account' => $this->account,
            'super_app_subscriptions' => $this->super_app_subscriptions,
            'foreign_currency_target' => $this->foreign_currency_target,
            'approval_status' => $this->approval_status,
            'created_by' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
