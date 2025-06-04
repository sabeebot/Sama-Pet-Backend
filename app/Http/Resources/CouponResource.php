<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'provider' => [
                'id' => $this->provider->id,
                'name' => $this->provider->provider_name_en,
            ],
            'created_date' => $this->created_date,
            'expiration_date' => $this->expiration_date,
            'title_en' => $this->title_en,
            'title_ar' => $this->title_ar,
            'description_en' => $this->description_en,
            'description_ar' => $this->description_ar,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'price_after' => $this->price_after,
            'discount' => $this->discount,
            'offer_type' => $this->offer_type,
            'audience' => $this->audience,
            'membership' => $this->membership,
            'code' => $this->code,
        ];
    }
}
