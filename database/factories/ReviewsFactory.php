<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Service;
use App\Models\Product;
use App\Models\PetOwner;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reviews>
 */
class ReviewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $serviceOrProduct = $this->faker->randomElement(['service', 'product']);
        return [
            'date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'rate' => fake()->numberBetween(0, 5),
            'comment' => fake()->sentence(),
            'service_id' => $serviceOrProduct == 'service' ? Service::factory() : null,
            'product_id' => $serviceOrProduct == 'product' ? Product::factory() : null,
            'pet_owner_id' => PetOwner::factory(),
        ];
    }
}
