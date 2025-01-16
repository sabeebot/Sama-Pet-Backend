<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
    
    // public function pet()
    // {
    //     return $this->belongsTo(Pet::class);
    // }

    public function membership()
    {
        return $this->hasMany(Membership::class);

    }
    
}
