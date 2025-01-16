<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fav extends Model
{
    use HasFactory;
    protected $fillable = [
        'pet_owner_id',
        'provider_id',
        'product_id',
        'service_id',
        'pet_id'
    ];
}
