<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponUsage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'date_of_usage',
        'coupon_id',
    ];

    public function owner()
    {
        return $this->belongsTo(PetOwner::class); 
    }

    public function coupon()
{
    return $this->belongsTo(Coupon::class, 'coupon_id');
}


    
}