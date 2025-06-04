<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    protected $fillable = [
        'invoice_date',
        'status',
        'customer_name',
        'contact_no',
        'email',
        'address',
        'delivery',
        'total_amount',
        'pet_owner_id',
    ];

    public function serviceItems()
    {
        return $this->hasMany(ServiceOrderItem::class);
    }

    public function petOwner()
    {
        return $this->belongsTo(PetOwner::class);
    }
}
