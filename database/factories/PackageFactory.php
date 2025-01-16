<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $duration = ['one month', 'one year'];
        $first_price = fake()->randomNumber(2, true);
        $second_price = fake()->numberBetween(5, $first_price);

        return [
            'is_free_trial' => fake()->boolean(),
            'description' => fake()->paragraph(1),
            'title' => fake()->sentence(3),
            'price' => $first_price,
            'duration' => fake()->randomElement($duration),
            'second_price' => $second_price,
            'status' => fake()->boolean(),
        ];
    }

    public function active(): self
    {
        return $this->state([
            'status' => 1,
        ]);
    }
}
