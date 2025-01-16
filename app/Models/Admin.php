<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasApiTokens;

    protected $guard = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'status',
        'profile_image',
        'contact_number'
    ];

    protected $table = 'admins';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The roles that belong to the admin.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_admin');
    }

    /**
     * Check if the admin has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        // Assuming roles are stored in a roles relationship
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Check if the admin has a specific permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('name', $permission);
        })->exists();
    }
}
