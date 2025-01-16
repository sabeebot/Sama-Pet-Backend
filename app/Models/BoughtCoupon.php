<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoughtCoupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_owner_id',
        'coupon_id',
    ];
}
