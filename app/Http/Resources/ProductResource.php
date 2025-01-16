<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'old_price' => $this->old_price,
            'new_price' => $this->new_price,
            'percentage' => $this->percentage,
            'quantity' => $this->quantity,
            'images' => $this->images,
            'description' => $this->description,
            'contact_number' => $this->contact_number,
            'pet_type' => $this->pet_type,
            'provider_id' => $this->provider_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
