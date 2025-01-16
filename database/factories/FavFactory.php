<?php

namespace Database\Factories;

use App\Models\Fav;
use App\Models\Product;
use App\Models\Service;
use App\Models\PetOwner;
use App\Models\Provider;
use App\Models\Pet;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavFactory extends Factory
{
    protected $model = Fav::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Randomly choose between product, service, and provider
        $type = $this->faker->randomElement(['product', 'service', 'provider', 'pet']);

        // Initialize the fields as null
        $providerId = null;
        $productId = null;
        $serviceId = null;
        $petId = null;

        // Set the fields based on the random choice
        switch ($type) {
            case 'product':
                $productId = Product::whereBetween('id', [1, 25])->inRandomOrder()->first()->id;
                break;
            case 'service':
                $serviceId = Service::whereBetween('id', [1, 25])->inRandomOrder()->first()->id;
                break;
            case 'provider':
                $providerId = Provider::whereBetween('id', [1, 25])->inRandomOrder()->first()->id;
                break;
            case 'pet':
                $petId = Pet::whereBetween('id', [1, 25])->inRandomOrder()->first()->id;
                break;
        }

        return [
            'pet_owner_id' => PetOwner::factory(),
            'provider_id' => $providerId,
            'product_id' => $productId,
            'service_id' => $serviceId,
            'pet_id' => $petId
        ];
    }
}
