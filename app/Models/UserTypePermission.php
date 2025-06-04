<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTypePermission extends Model
{
    protected $fillable = ['user_type', 'permission_id'];

    public $timestamps = false;

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
}
