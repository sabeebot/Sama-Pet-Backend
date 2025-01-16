<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderResource extends JsonResource
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
            'profile_image' => $this->profile_image,
            'availablity' => $this->availablity,
            'timing' => $this->timing,
            'type' => $this->type,
            'name' => $this->name,
            'email' => $this->email,
            'address' => $this->address,
            'contact_no' => $this->contact_no,
            'social_media' => $this->social_media,
            'documents' => $this->documents,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'services' => ServiceResource::collection($this->whenLoaded('services')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
