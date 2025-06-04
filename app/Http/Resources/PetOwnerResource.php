<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetOwnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
{
    return [
        'id'             => $this->id,
        'startDate'      => $this->created_at, // Use created_at as start date
        'ownerName'      => $this->first_name . ' ' . $this->last_name,
        'first_name'  => $this->first_name, // Added first name
        'last_name'   => $this->last_name,  // Added last name
        'email'          => $this->email, // New field
        'gender'         => $this->gender,  // Add gender
        'status'         => $this->status,   // New field for status
        'nationality'    => $this->nationality, // New field
        'location'       => $this->location, // New field
        'date_of_birth'  => $this->date_of_birth, // New field
        'city'           => $this->city, // New field
        'house'          => $this->house, // New field
        'road'           => $this->road, // New field
        'block'          => $this->block, // New field
        'building'       => $this->building, // New field
        'apt_number'     => $this->apt_number, // New field
        'floor'          => $this->floor, // New field
        'company'        => $this->company, // New field
        'contactNumber'  => $this->phone,
        'noOfPets'       => $this->pets ? $this->pets->count() : 0,
        'issueDate'      => $this->created_at,
        'expiryDate'     => $this->expiry_date ?? '',
        'imageUrl'       => $this->profile_image,
        'type'           => $this->status,
        'period'         => '',
        'amount'         => '',
        'status'         => $this->status,
        'pets'           => $this->pets,
    ];
}

}
