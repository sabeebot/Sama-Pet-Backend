<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    // Disable automatic timestamps if not needed
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'membership_id'
    ];

    /**
     * Get the order associated with the invoice.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the membership associated with the invoice.
     */
    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }
}
