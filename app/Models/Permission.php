<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permission extends Model
{
    protected $fillable = ['name'];

    public $timestamps = false;

    

    public function userTypePermissions(): HasMany
    {
        return $this->hasMany(UserTypePermission::class);
    }
}

