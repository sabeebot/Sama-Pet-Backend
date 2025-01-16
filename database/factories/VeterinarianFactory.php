<?php

namespace Database\Factories;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class VeterinarianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'years_of_experience' => fake()->randomNumber(2),
            'picture' => fake()->imageUrl(640, 480, 'person', true),
            'specialization' => fake()->sentence(5),
            'education' => fake()->sentence(3),
            'bio' => fake()->sentence(15),
        ];
    }
}
