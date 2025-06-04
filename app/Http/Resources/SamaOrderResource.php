<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SamaOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'               => $this->id,
            'date_created'     => $this->start_date,  // Using start_date as the creation date
            'date_received'    => $this->received_date,
            'product_name'     => $this->product_name_en,  // Change this if you want to use product_name_ar
            'product_name_ar'  => $this->product_name_ar,  // Arabic product name added
            'pet_type'         => $this->pet_type,
            'quantity'         => $this->quantity,
            'price'            => $this->price,
            'delivery_charges' => $this->delivery_price,
            'status'           => $this->status,
            'supplierId'       => $this->petsupplier_id,
        ];
    }
}
