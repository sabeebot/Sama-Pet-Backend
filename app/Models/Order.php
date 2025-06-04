<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Define fillable fields for mass assignment.
    // Removed invoice_number and order_number since the primary key id will auto increment.
    protected $fillable = [
        'invoice_date',
        'status',
        'customer_name',
        'contact_no',
        'email',
        'address',
        'delivery',
        'total_amount',
        'pet_owner_id'  // optional relationship to PetOwner.
    ];

    // Relationship: Invoice belongs to a PetOwner.
    public function petOwner()
    {
        return $this->belongsTo(PetOwner::class);
    }

    public function OrderProducts()
{
    return $this->hasMany(OrderProduct::class);
}


public function products()
{
    return $this->belongsToMany(Product::class, 'order_products', 'order_id', 'product_id');
}

}
