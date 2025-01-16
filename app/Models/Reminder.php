<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Reminder extends Model
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        'pet_owner_id',
        'pet_id',
        'title',
        'date',
        'time',
        'remind',
        'repeat',
        'note' ,
        'provider_id'
    ];

    // Define the relationship with the PetOwner model
    public function petOwner()
    {
        return $this->belongsTo(PetOwner::class);
    }

    // Define the relationship with the Pet model
    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
