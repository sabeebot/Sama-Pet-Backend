<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pet_owner_id',
        'provider_id',
        'card_id',
        'payment_method',
        'amount',
        'discount_amount',
        'currency',
        'transaction_id',
        'status',
        'description',
        'payment_date',
        'metadata',
        'order_id',
        'coupon_id',
        'package_id',
        
    ];

    /**
     * Get the pet owner who made the payment.
     */
    public function petOwner()
    {
        return $this->belongsTo(PetOwner::class);
    }

    /**
     * Get the provider who received the payment.
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the card used for the payment.
     */
    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}

