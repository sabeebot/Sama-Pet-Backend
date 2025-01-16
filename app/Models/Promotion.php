<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'type',
        'ad_image',
        'logo_image',
        'business_image',
        'business_name',
        'ad_description',
        'phone_number',
    ];
    protected $casts = [
        'social_media' => 'json',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}