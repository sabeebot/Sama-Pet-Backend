<?php

namespace Database\Factories;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pet_type = ['Dogs', 'Cats', 'Birds', 'Fish', 'Hamster', 'Guinea Pig', 'Rabbit'];
        $json = json_encode($pet_type);
        $images = [];
        for ($i = 0; $i < rand(1, 5); $i++) {
            $images[] = fake()->imageUrl(640, 480, 'product', true);
        }
        $json_images = json_encode($images);
        return [
            'name' => fake()->words(3, true),
            'old_price' => fake()->randomFloat(2, 1, 1000),
            'new_price' => fake()->randomFloat(2, 1, 1000),
            'percentage' => fake()->numberBetween(0, 100),
            'quantity' => fake()->numberBetween(1, 100),
            'images' => $json_images,
            'description' => fake()->paragraph(2),
            'contact_number' => fake()->phoneNumber(),
            'pet_type' => $json,
        ];
    }
}
