<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;
    protected $table = 'product_categories';

    // Define the fillable attributes
    protected $fillable = [
        'id',
        'provider_id',
        'name',
        'total_stock',
        'selected_category',
        'selected_subcategory',
        'description',
        'price',
        'status',
        'availability',
        'total_sold',
        'image_url',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

   
}
