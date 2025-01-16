<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReminderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'pet_owner_id' => $this->pet_owner_id,
            'pet_id' => $this->pet_id,
            'title' => $this->title,
            'date' => $this->date,
            'time' => $this->time,
            'remind' => $this->remind,
            'repeat' => $this->repeat,
            'note' => $this->note,
            'provider_id' => $this->provider_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
