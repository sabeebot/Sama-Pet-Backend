<?php

namespace Database\Factories;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Service;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $fakerAr = \Faker\Factory::create('ar_SA'); // Use 'ar_SA' or 'ar' for Arabic locale

        $pet_type = ['Dogs', 'Cats', 'Birds', 'Fish', 'Hamster', 'Guinea Pig', 'Rabbit'];
        $json = json_encode($pet_type);

        return [
            'title' => fake()->words(3, true),
            'short_description' => fake()->paragraph(2),
            'old_price' => fake()->randomFloat(2, 1, 1000),
            'new_price' => fake()->randomFloat(2, 1, 1000),
            'percentage' => fake()->numberBetween(0, 100),
            'contact_number' => fake()->phoneNumber(),
            'pet_type' => $json,
            'image' => fake()->imageUrl(640, 480, 'services', true),
            'short_description_ar' => $fakerAr->paragraph(2), // Use Arabic Faker
            'title_ar' => $fakerAr->words(3, true), // Use Arabic Faker

        ];
    }
}
