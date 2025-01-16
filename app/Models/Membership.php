<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'start_date',
        'end_date',
        'package_id',
        'pet_id',
    ];

    // Define an inverse one-to-many relationship with Package
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    // Define an inverse one-to-many relationship with Pet
    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
