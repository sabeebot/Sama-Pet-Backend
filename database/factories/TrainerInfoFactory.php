<?php

namespace Database\Factories;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrainerInfo>
 */
class TrainerInfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'years_of_experience' => fake()->randomNumber(2),
            'medical_degree_and_specializtion' => fake()->sentence(),
            'provider_id' => Provider::factory()->trainer(),
        ];
    }
}
