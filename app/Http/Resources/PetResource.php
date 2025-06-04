<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetResource extends JsonResource
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
            'age' => $this->age,
            'gender' => $this->gender,
            'image' => $this->image,
            'weight' => $this->weight,
            'height' => $this->height,
            'pet_type' => $this->pet_type,
            'breed' => $this->breed,
            'color' => $this->color,
            'is_vaccinated' => $this->is_vaccinated,
            'is_microchipped' => $this->is_microchipped,
            'is_neutered' => $this->is_neutered,
            'is_lost' => $this->is_lost,
            'price' => $this->price,
            'allow_selling' => $this->allow_selling,
            'allow_adoption' => $this->allow_adoption,
            'pet_owner_id' => $this->pet_owner_id,
            'description' => $this->description,
            'membership'    => $this->whenLoaded('membership'),
            
        ];
    }
}
