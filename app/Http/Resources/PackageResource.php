<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'is_free_trial' => $this->is_free_trial,
            'description' => $this->description,
            'title' => $this->title,
            'price' => $this->price,
            'duration' => $this->duration,
            'second_price' => $this->second_price,
            'status' => $this->status,
        ];
    }
}
