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
        'id'                => $this->id,
        'is_free_trial'     => $this->is_free_trial,
        'description'       => $this->description,
        'title'             => $this->title,
        'price'             => $this->price,
        'duration'          => $this->duration,
        'second_price'      => $this->second_price,
        'status_staff'      => $this->status_staff,
        'status_user'       => $this->status_user,
        'title_ar'          => $this->title_ar,
        'description_ar'    => $this->description_ar,
        'start_date'        => $this->start_date,
        'end_date'          => $this->end_date,
        'default_delivery'  => $this->default_delivery,
        'apply_first_offer' => $this->apply_first_offer,
        'apply_second_offer'=> $this->apply_second_offer,
    ];
}



}
