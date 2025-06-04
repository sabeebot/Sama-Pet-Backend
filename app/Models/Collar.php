<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collar extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'url',
        'pet_id', 
        'product_id'
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
