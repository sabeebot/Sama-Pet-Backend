<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\OrderProductResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'            => $this->id,
            'invoice_date'  => $this->invoice_date,       // Date when the order was created
            'customer_name' => $this->customer_name,      // Customer name from the order
            'contact_no'    => $this->contact_no,         // Customer contact number
            'email'         => $this->email,              // Customer email
            'address'       => $this->address,            // Customer address
            'status'        => $this->status,             // Order status (e.g. Pending)
            'delivery'      => $this->delivery,           // Delivery charges or method
            'total_amount'  => $this->total_amount,       // Total order amount
            'pet_owner_id'  => $this->pet_owner_id,       // Associated pet owner id
            // Include related order products:
            'orderProducts' => OrderProductResource::collection($this->whenLoaded('OrderProducts')),
        ];
    }
}
