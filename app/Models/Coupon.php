<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_id',
        'quantity',
        'expiration_date',
        'description',
        'membership',
        'code',
        'price',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
