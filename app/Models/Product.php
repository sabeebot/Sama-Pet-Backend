<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

        // Fillable attributes
        protected $fillable = [
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'name',
            'old_price',
            'new_price',
            'quantity',
            'percentage',
            'contact_number',
            'pet_type',
            'status',
            'provider_id',
            'category_id',
            'product_name_en',
            'product_description_en',
            'product_description_ar',
            'amount',
            'discount',
            'image_url',
        ];
    
        // Casts attributes
        protected $casts = [
            'service_id' => 'integer',
            'product_id' => 'integer',
            'pet_owner_id' => 'integer',
            'rate' => 'integer',
            'date' => 'date',
        ];

    public function providers()
    {
        return $this->belongsTo(Provider::class);
    }

    public function Reviews()
    {
        return $this->hasMany(Reviews::class);
    }
}
