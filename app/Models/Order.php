<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_owner_id',
        'order_date',
        'amount',
        'discount_amount',
        'metadata',
        'status'
    ];
}