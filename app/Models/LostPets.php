<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostPets extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gender',
        'pet_type',
        'breed',
        'color',
        'image',
        'location',
        'description',
        'pet_owner_id',
        'role',

    ];

    // Relationship to the pet owner who found the lost pet
    public function founder()
    {
        return $this->belongsTo(PetOwner::class, 'pet_owner_id');
    }

    // Optional relationship to a pet in the pets table, if applicable
    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id')->nullable();
    }
}
