<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QrCode>
 */
class QrCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'pet_id' => fake()->unique()->numberBetween(1, 1000),
        ];
    }

    public function forPet(int $petId)
    {
        return $this->state([
            'pet_id' => $petId,
        ]);
    }
}
