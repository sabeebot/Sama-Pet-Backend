<?php

namespace App\Models;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'title',
        'title_ar',
        'short_description',
        'short_description_ar',
        'old_price',
        'new_price',
        'percentage',
        'image',
        'contact_number',
        'pet_type',
        'status',
        'provider_id',
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
