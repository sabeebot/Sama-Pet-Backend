<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'service_id' => $this->service_id,
            'product_id' => $this->product_id,
            'pet_owner_id' => $this->pet_owner_id,
            'rate' => $this->rate,
            'comment' => $this->comment,
            'date' => $this->date,
        ];
    }
}
