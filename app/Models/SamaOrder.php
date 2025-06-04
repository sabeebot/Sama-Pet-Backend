<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SamaOrder extends Model
{
    use HasFactory;
    public $timestamps = false; // Disable automatic timestamps

    protected $fillable = [
        'product_name_en',
        'product_name_ar',
        'pet_type',
        'status',
        'supplier_id',
        'quantity',
        'start_date',
        'received_date',
        'price',
        'delivery_price',
    ];
}

