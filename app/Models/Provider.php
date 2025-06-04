<?php

namespace App\Models;

use App\Models\Service;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Provider extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

        protected $fillable = [
            'type',
            'name',
            'contact_no',
            'profile_image',
            'email',
            'provider_name_en',
            'provider_name_ar',
            'cr_number',
            'instagram',
            'office',
            'road',
            'block',
            'start_date',
            'end_date',
            'website',
            'city',
            'address',
            'status',
            'social_media',
            'documents',
            'password',
            'availability_days',
            'availability_hours',
            'authorized_persons',
            
            
        ];
                         // Add type to the fillable


    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'documents' => 'array',
        'social_media' => 'array',
        'availability' => 'array'   // Ensure the availability field is cast to an array
    ];

    // Relationship to services, products, and veterinarians
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function products_catgory()
{
    return $this->hasMany(ProductCategory::class);
}

  
    public function Gallery()
    {
        return $this->hasOne(Gallery::class);
    }
    
    public function veterinarians()
    {
        return $this->hasMany(Veterinarian::class);
    }

    // Add relationship for extra doctor and trainer info
    public function doctorInfo()
    {
        return $this->hasOne(DoctorInfo::class);
    }

    public function trainerInfo()
    {
        return $this->hasOne(TrainerInfo::class);
    }

    public function coupons()
{
    return $this->hasMany(Coupon::class, 'provider_id');
}

}