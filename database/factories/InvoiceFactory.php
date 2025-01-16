<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
        'date' =>fake()->dateTimeBetween('-1 year', 'now')-> format('Y-m-d'),
        'amount' => fake()-> randomFloat(2, 1, 1000),
        'document_name' => fake()->uuid(),
        ];
    }
}
