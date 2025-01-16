<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\PetOwner; // Assuming you have this model
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product; 

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'pet_owner_id' => PetOwner::factory(), // Creates a pet owner if necessary
            'order_date' => $this->faker->dateTime(),
            'amount' => $this->faker->randomFloat(2, 10, 1000), // Random amount between 10 and 1000
            'discount_amount' => $this->faker->randomFloat(2, 0, 100), // Random discount up to 100
            'metadata' => json_encode([
                'products' => $this->getProductsData(), // Call method to get multiple products
            ]),
        ];
    }

    protected function getProductsData()
    {
        $products = [];
        $numProducts = $this->faker->numberBetween(2, 5); // Number of products per order (at least 2)

        // Select a random product to determine the provider
        $initialProduct = Product::inRandomOrder()->first();
        $providerId = $initialProduct->provider_id;

        // Add the initial product
        $products[] = [
            'product_id' => $initialProduct->id,
            'product_name' => $initialProduct->name,
            'amount' => $this->faker->numberBetween(1, 500), 
            'provider_id' => $providerId,
            'price' => $initialProduct->old_price, 
        ];

        // Add more products from the same provider
        for ($i = 1; $i < $numProducts; $i++) {
            $product = Product::where('provider_id', $providerId)->inRandomOrder()->first(); // Ensure the same provider
            $products[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'amount' => $this->faker->numberBetween(1, 500), 
                'provider_id' => $providerId,
                'price' => $product->old_price, 

            ];
        }

        return $products;
    }
}


