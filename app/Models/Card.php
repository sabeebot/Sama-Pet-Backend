<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_id',
        'pet_owner_id',
        'card_number',
        'cardholder_name',
        'expiration_date',
        'card_type',
        'cvv',
        'is_default',
    ];

    /**
     * Get the provider associated with the card.
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the pet owner associated with the card.
     */
    public function petOwner()
    {
        return $this->belongsTo(PetOwner::class);
    }
}
