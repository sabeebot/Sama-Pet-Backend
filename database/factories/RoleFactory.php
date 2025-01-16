<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Role;
use App\Models\Permission;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'super admin',
                'manager',
                'employee',
                'trainee'
            ]),
        ];
    }

    public function assignPermissions(Role $role)
    {
        $permissions = Permission::all()->keyBy('name');

        switch ($role->name) {
            case 'super admin':
                $role->permissions()->attach([
                    $permissions['manage providers']->id,
                    $permissions['edit users']->id,
                    $permissions['create users']->id,
                    $permissions['view reports']->id,
                    $permissions['delete records']->id,
                    $permissions['create role']->id,
                    $permissions['assign permissions']->id
                ]);
                break;
            case 'manager':
                $role->permissions()->attach([
                    $permissions['edit users']->id,
                    $permissions['create users']->id,
                    $permissions['view reports']->id,
                    $permissions['delete records']->id,
                ]);
                break;
            case 'employee':
                $role->permissions()->attach([
                    $permissions['create users']->id,
                    $permissions['view reports']->id,
                    $permissions['delete records']->id,
                ]);
                break;
            case 'trainee':
                $role->permissions()->attach([
                    $permissions['create users']->id,
                    $permissions['view reports']->id,
                ]);
                break;
        }
    }
}
