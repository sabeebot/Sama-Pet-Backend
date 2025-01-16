<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingProvider extends Model
{
    use HasFactory;

    // The table associated with the model
    protected $table = 'pending_providers';

    // The attributes that are mass assignable
    protected $fillable = [
        'name',
        'email',
        'contact_no',
        'address',
        'type',
        'status',  // Status will be pending by default
        'password', // Include password here
        'token' ,
    ];

    // The attributes that should be hidden for serialization
    protected $hidden = [
        'password',
    ];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }
}
