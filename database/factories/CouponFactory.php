<?php

namespace Database\Factories;

use App\Models\Coupon;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CouponFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Coupon::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $provider = Provider::factory()->create();
        return [
            'provider_id' => Provider::factory(),
            'title' => $this->faker->sentence(nbWords: 10),
            'image' => fake()->imageUrl(width:640, height:480, category: 'people', randomize: true, word: 'pet'),
            'quantity' => $this->faker->numberBetween(1, 100),
            'code' => $this->faker->unique()->word(),
            'expiration_date' => $this->faker->dateTimeBetween('now', '+5 year'),
            'description' => $this->faker->sentence(10),
            'membership' => $this->faker->boolean(),
            'price' => $this->faker->randomFloat(2, 0, 99.99),
        ];
    }
}