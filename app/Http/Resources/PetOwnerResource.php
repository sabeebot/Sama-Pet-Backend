<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetOwnerResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'nationality' => $this->nationality,
            'date_of_birth' => $this->date_of_birth,
            'location' => $this->location,
            'phone' => $this->phone,
            'city' => $this->city,
            'gender' => $this->gender,
            'house' => $this->house,
            'road' => $this->road,
            'block' => $this->block,
            'bulding_name' => $this->building_name,
            'apt_number' => $this->apt_number,
            'floor' => $this->floor,
            'company' => $this->company,
            'profile_image' => $this->profile_image,
            'building_name' => $this->building_name,
            'created_at' => $this->created_at,
            'status' => $this->status,
            // 'pets' => PetResource::collection($this->whenLoaded('pets')),
        ];
    }
}
