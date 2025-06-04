<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    public $timestamps = false; // Disable automatic timestamps

    protected $fillable = [
        'product_name_en',
        'product_name_ar',
        'pet_type',                     // Use this field instead of "type_of_pet"
        'product_description_en',       // New field (nullable)
        'product_description_ar',       // New field (nullable)
        'status',
        'quantity',
        'price_before',                 // New field (nullable)
        'price_after',                  // New field (nullable)
        'discount',                     // New field (nullable)
        'provider_id',                  // New field (nullable)
        'category_id',                  // New field (nullable)
        'start_date',
        'received_date',
        'price',
        'delivery_price',
        'image_url',
    ];
    
    // Casts for attributes, including the new ones
    protected $casts = [
        'service_id'   => 'integer',
        'product_id'   => 'integer',
        'pet_owner_id' => 'integer',
        'rate'         => 'integer',
        'category_id'  => 'integer',
        'quantity'     => 'integer',
        'price_before' => 'float',
        'price_after'  => 'float',
        'discount'     => 'float',
        'provider_id'  => 'integer',
        'date'         => 'date',
    ];

    public function providers()
    {
        return $this->belongsTo(Provider::class);
    }

    public function reviews()
    {
        return $this->hasMany(Reviews::class);
    }
}
