<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'          => $this->id,
            'name'       => $this->name,        // ✅ Ensure it matches HTML
            'key_person' => $this->key_person,
            'contact1'    => $this->contact1,
            'contact2'    => $this->contact2 ?? '',
            'email'       => $this->email,
            'address'     => $this->address,
            'website'     => $this->website ?? '',
            'instagram'   => $this->instagram ?? '',
            'profileImage'=> $this->profile_image ?? '',
            'createdAt'   => $this->created_at,
        ];
    }
}
