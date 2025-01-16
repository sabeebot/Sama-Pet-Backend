<?php

namespace Database\Factories;

use App\Models\PetOwner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PetOwnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fName = fake()->unique()->firstName();
        $nationalities = [
            'Algerian',
            'Bahraini',
            'Comorian',
            'Djiboutian',
            'Egyptian',
            'Iraqi',
            'Jordanian',
            'Kuwaiti',
            'Lebanese',
            'Libyan',
            'Mauritanian',
            'Moroccan',
            'Omani',
            'Palestinian',
            'Qatari',
            'Saudi',
            'Somali',
            'Sudanese',
            'Syrian',
            'Tunisian',
            'Emirati',
            'Yemeni',
        ];

        $locationType = fake()->randomElement(['house', 'apartment', 'office']);

        $locationData = [];
        switch ($locationType) {
            case 'house':
                $locationData = [
                    'city' => fake()->city(),
                    'house' => fake()->buildingNumber(),
                    'road' => fake()->streetName(),
                    'block' => fake()->secondaryAddress(),
                ];
                break;
            case 'apartment':
                $locationData = [
                    'city' => fake()->city(),
                    'apt_number' => fake()->buildingNumber(),
                    'building_name' => fake()->company(),
                    'floor' => fake()->randomDigitNotNull(),
                    'road' => fake()->streetName(),
                    'block' => fake()->secondaryAddress(),
                ];
                break;
            case 'office':
                $locationData = [
                    'city' => fake()->city(),
                    'company' => fake()->company(),
                    'building_name' => fake()->companySuffix(),
                    'floor' => fake()->randomDigitNotNull(),
                    'road' => fake()->streetName(),
                    'block' => fake()->secondaryAddress(),
                ];
                break;
        }

        $phone = fake()->numerify('###-###-####');

        return array_merge([
            'password' => password_hash($fName . '123', PASSWORD_DEFAULT),
            'first_name' => $fName,
            'last_name' => fake()->lastName(),
            'email' => "$fName@gmail.com",
            'phone' => $phone,
            'gender' => fake()->randomElement(["m", "f"]),
            'nationality' => fake()->randomElement($nationalities),
            'profile_image' => fake()->imageUrl(640, 480, 'people', true, 'pet'),
            'location' => $locationType,
            'date_of_birth' => fake()->date(),
            'status' => fake()->randomElement(['Member', 'Non-member', 'Free trial']),
        ], $locationData);
    }
}
