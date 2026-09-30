<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'grade' => $this->grade,
            'district' => $this->whenLoaded('district', function () {
                return [
                    'id' => $this->district->id,
                    'name' => $this->district->name,
                    'location_type' => $this->district->location_type,
                ];
            }),
            'bankingType' => $this->whenLoaded('bankingType', function () {
                return [
                    'id' => $this->bankingType->id,
                    'name' => $this->bankingType->name,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
