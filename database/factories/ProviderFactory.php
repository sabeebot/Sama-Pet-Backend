<?php

namespace Database\Factories;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class ProviderFactory extends Factory
{
    protected $model = Provider::class;

    public function definition(): array
    {
        $type = [
            'pet shop',
            'groomer',
            'pet clinic',
        ];

        // Social media structure as an associative array
        $social = [
            'insta' => $this->faker->uuid(),
            'twitter' => $this->faker->uuid(),
            'website' => $this->faker->uuid()
        ];

        // Availability days structure
        $availabilityDays = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        $randomDays = $this->faker->randomElements($availabilityDays, $this->faker->numberBetween(1, 7));

        // Timing structure
        $timing = [
            'from' => $this->faker->time('H:i'),
            'to' => $this->faker->time('H:i'),
        ];

        $name = $this->faker->unique()->words(3, true);

        return [
            'type' => $this->faker->randomElement($type),
            'name' => $name,
            'email' => $this->faker->unique()->safeEmail(),
            'password' => password_hash($name . '123', PASSWORD_DEFAULT),
            'address' => $this->faker->city() . ', ' . $this->faker->streetAddress() . ', ' . $this->faker->postcode(),
            'timing' => json_encode($timing),  // Timing is stored as JSON
            'contact_no' => $this->faker->randomNumber(8),
            'profile_image' => $this->faker->imageUrl(640, 480, 'people', true, 'pet'),
            'social_media' => json_encode($social),  // Social media is stored as JSON
            'availability' => json_encode($randomDays),  // Availability is stored as JSON
            'documents' => json_encode([
                'contract' => $this->faker->uuid(),
                'certification' => $this->faker->uuid(),
                'cr_record' => $this->faker->uuid()
            ]),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }

    // Doctor state definition
    public function doctor()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'doctor',
            ];
        });
    }

    // Trainer state definition
    public function trainer()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'trainer',
            ];
        });
    }
}
