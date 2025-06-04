<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{

    // Add all the fields that should be mass-assignable
    protected $fillable = [
        'name',
        'age',
        'weight',
        'height',
        'pet_type',
        'breed',
        'color',
        'image',
        'gender',
        'is_vaccinated',
        'is_microchipped',
        'is_neutered',
        'allow_selling',
        'price',
        'description',
        'is_lost',
        'allow_adoption',
        'pet_owner_id',
        'documents', // if documents is a JSON string or handled as text
    ];

    use HasFactory;

    protected $casts = [
        'documents' => 'array', // Automatically cast JSON to array
    ];

    public function petOwner()
    {
        return $this->belongsTo(PetOwner::class);
    }

    public function owner()
{
    return $this->belongsTo(PetOwner::class, 'pet_owner_id');
}


    public function QrCode()
    {
        return $this->hasOne(QrCode::class);
    }

    public function membership()
    {
        return $this->hasOne(Membership::class);
    }

    // this was also added check if pets will work
    protected $table = 'pets';
}
