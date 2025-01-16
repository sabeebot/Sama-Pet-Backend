<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LostPets;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LostPets>
 */
class LostPetsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = LostPets::class;
    public function definition(): array
    {
        return [
            'gender' => $this->faker->randomElement(['m', 'f']),
            'name' => $this->faker->name,
            'pet_type' => $this->faker->randomElement(['dog', 'cat', 'bird', 'other']),
            'breed' => $this->faker->word,
            'color' => $this->faker->safeColorName,
            'location' => $this->faker->city,
            'image' => $this->faker->imageUrl(640, 480, 'animals', true), // Simulate an image URL
            'description' => $this->faker->text(32),
            'pet_owner_id' => \App\Models\PetOwner::factory(),
            'role' => $this->faker->randomElement(['Founder', 'Owner']),
        ];
    }
}
