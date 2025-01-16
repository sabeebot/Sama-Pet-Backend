<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Reviews extends Model
{
    use HasFactory, HasApiTokens, Notifiable;
    
    protected $fillable = [
        'service_id',
        'product_id',
        'pet_owner_id',
        'rate',
        'comment',
        'date',
    ];

    public function services()
    {
        return $this->belongsTo(Service::class);
    }

    public function products()
    {
        return $this->belongsTo(Product::class);
    }

    public function petOwners()
    {
        return $this->belongsTo(PetOwner::class);
    }
}
