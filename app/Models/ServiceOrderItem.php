<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'service_order_id',
        'service_id',
        'quantity',
        'unit_price',
        'discount_percentage',
        'total_price'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class);
    }
}
