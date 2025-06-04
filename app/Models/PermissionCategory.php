<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermissionCategory extends Model
{
    protected $fillable = ['name'];

    public $timestamps = false;

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }
}
