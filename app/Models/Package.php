<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'is_free_trial',
        'description',
        'title',
        'price',
        'duration',
        'second_price',
        'status_user',
        'status_staff',
        'start_date',
        'end_date'
    ];
    
    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'is_free_trial' => 'boolean',
        'price'         => 'decimal:2',
        'second_price'  => 'decimal:2',
        'status_user'   => 'boolean',
        'status_admin'  => 'boolean',
        'start_date'    => 'date',
        'end_date'      => 'date',
    ];
    
    public function membership()
    {
        return $this->hasMany(Membership::class);
    }
}
