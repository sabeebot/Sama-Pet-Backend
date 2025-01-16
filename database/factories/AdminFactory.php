<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AdminFactory extends Factory
{
    /**
     * The name of the model that this factory corresponds to.
     *
     * @var string
     */
    protected $model = Admin::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $firstName = $this->faker->firstName;
        return [
            'first_name' => $firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt($firstName . '123'),
            'role' => $this->faker->randomElement(['super admin', 'manager', 'employee', 'trainee', 'agent']),
            'status' => $this->faker->randomElement(['active', 'non active', 'cancelled']),
            'profile_image' => $this->faker->imageUrl(640, 480, 'people', true),
            'contact_number' => $this->faker->phoneNumber()
        ];
    }
}
