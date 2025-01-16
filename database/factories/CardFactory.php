<?php

namespace Database\Factories;

use App\Models\Card;
use App\Models\Provider;
use App\Models\PetOwner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CardFactory extends Factory
{
    protected $model = Card::class;

    public function definition()
    {
        return [
            'provider_id' => Provider::factory(), // Generate a provider or leave null
            'pet_owner_id' => PetOwner::factory(), // Generate a pet owner or leave null
            'card_number' => $this->faker->creditCardNumber(),  // Generate a fake credit card number
            'cardholder_name' => $this->faker->name(),  // Generate a fake cardholder name
            'expiration_date' => $this->faker->creditCardExpirationDate(),  // Fake expiration date
            'card_type' => $this->faker->randomElement(['Visa', 'MasterCard', 'Amex']),  // Random card type
            'cvv' => Str::random(3),  // Random CVV (3 digits)
            'is_default' => $this->faker->boolean(),  // Randomly set as default
        ];
    }
}
