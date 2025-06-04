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
            'id'                      => $this->id,
            'product_name_en'         => $this->product_name_en,
            'product_name_ar'         => $this->product_name_ar,
            'product_description_en'  => $this->product_description_en,
            'product_description_ar'  => $this->product_description_ar,
            'pet_type'                => $this->pet_type,
            'category_id'             => $this->category_id,
            'quantity'                => $this->quantity,
            'price_before'            => $this->price_before,
            'price'                   => $this->price,  // current price (from "price-after" input)
            'discount'                => $this->discount,
            'provider_id'             => $this->provider_id,
            'status'                  => $this->status,
            'created_at'              => $this->created_at,
            'updated_at'              => $this->updated_at,
        ];
    }
}
