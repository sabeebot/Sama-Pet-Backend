<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;



class PetOwner extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'nationality',
        'profile_image',
        'location',
        'city',
        'gender',
        'date_of_birth',
        'house',
        'road',
        'block',
        'building',
        'apt_number',
        'floor',
        'company',
        'status'
    ];

    protected $hidden = [
        'password',
    ];


    //public function getStatusAttribute()
    //{
        //return $this->userStatus();
    //}

    private function userStatus()
    {
        $pets = $this->pets;
        $isM = false;
        $isFreeTrial = false;
        for ($i = 0; $i < count($pets); $i++) {
            $membership = Membership::where('pet_id', $pets[$i]->id)->first();
            if ($membership) {
                $isM = true;
                $package = $membership->package;
                $package->is_free_trial ? $isFreeTrial = true : $isFreeTrial = false;
                if ($isFreeTrial) {
                    return 'Free trial';
                }
            }
        }
        if ($isM) {
            return 'Member';
        } else {
            return 'Non-member';
        }
    }


    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function reviews()
    {
        return $this->hasMany(Reviews::class);
    }
    public function lostPets()
    {
        return $this->hasMany(LostPets::class);
    }
}
