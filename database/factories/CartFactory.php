<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Product;
use App\Models\PetOwner;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pet_owner_id' => PetOwner::factory(),
            'product_id' => Product::whereBetween('id', [1, 25])->inRandomOrder()->first()->id,
            'quantity' => $this->faker->numberBetween(1, 5),
            'pet_id' => null,
        ];
    }
}
