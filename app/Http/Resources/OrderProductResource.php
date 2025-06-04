<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductResource;

class OrderProductResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id'                  => $this->id,
            'order_id'            => $this->order_id,
            'product_id'          => $this->product_id,
            'quantity'            => $this->quantity,
            'unit_price'          => $this->unit_price,
            'discount_percentage' => $this->discount_percentage,
            'total_price'         => $this->total_price,
            'product'             => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
