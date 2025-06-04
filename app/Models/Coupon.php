<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;
    protected $fillable = [
        'provider_id',
        'created_date',      // The date when the coupon was created.
        'expiration_date',   // This will store the valid_till date.
        'title_en',          // Coupon title in English.
        'title_ar',          // Coupon title in Arabic.
        'description_en',    // Coupon description in English.
        'description_ar',    // Coupon description in Arabic.
        'quantity',
        'price',             // Original price.
        'price_after',       // Price after discount.
        'discount',          // Discount percentage.
        'offer_type',        // If free, this should be 0; if paid, it will be the paid price (stored as an integer).
        'audience',          // Either "Sama Member" or "All Users".
        // Optionally, include these if you plan to use them:
        'membership',
        'code'
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function couponUsages()
{
    return $this->hasMany(CouponUsage::class, 'coupon_id');
}


}
