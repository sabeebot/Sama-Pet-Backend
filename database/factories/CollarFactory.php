<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Pet;

class CollarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $number = $this->faker->unique()->numberBetween(00000, 99999);

        return [
            'url' => "http://localhost:4300/user-main-component/find_pet/SM{$number}", 
            'pet_id' => null, 
            'product_id' => null,
        ];
    }
}
