<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\Pet;
use App\Models\PetOwner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $petTypes = [
            'Dogs' => ['Labrador Retriever', 'German Shepherd', 'Golden Retriever', 'Bulldog', 'Beagle'],
            'Cats' => ['Siamese', 'Persian', 'Maine Coon', 'Bengal', 'Sphynx'],
            'Birds' => ['Parakeet', 'Canary', 'Cockatiel', 'African Grey Parrot', 'Lovebird'],
            'Fish' => ['Betta', 'Goldfish', 'Guppy', 'Neon Tetra', 'Angelfish'],
            'Hamster' => ['Syrian', 'Dwarf'],
            'Guinea Pig' => ['American', 'Abyssinian'],
            'Rabbit' => ['Holland Lop', 'Netherland Dwarf'],
        ];

        $petType = fake()->randomElement(array_keys($petTypes));
        $breed = fake()->randomElement($petTypes[$petType]);
        $allowSelling = fake()->boolean();
        
        // Define base attributes
        $petAttributes = [
            'gender' => fake()->randomElement(["m", "f"]),
            'name' => fake()->firstName(),
            'age' => fake()->date('Y-m-d'),
            'weight' => fake()->randomFloat(1, 5, 50),
            'height' => fake()->randomFloat(1, 5, 50),
            'pet_type' => $petType,
            'breed' => $breed,
            'color' => fake()->colorName(),
            'image' => fake()->imageUrl(640, 480, 'animals', true),
            'is_vaccinated' => fake()->randomElement(["yes", "no", "I don't know"]),
            'allow_selling' => $allowSelling,
            'is_microchipped' => fake()->randomElement(["yes", "no", "I don't know"]),
            'is_neutered' => fake()->randomElement(["yes", "no", "I don't know"]),
            'is_lost' => fake()->boolean(),
            'allow_adoption' => fake()->boolean(),
            'documents' => json_encode([
                'password' => fake()->uuid(),
            ]),
        ];

        // Set price only if selling is allowed
        if ($allowSelling) {
            $petAttributes['price'] = fake()->randomFloat(2, 50, 500);
            $petAttributes['description'] = substr(fake()->sentence(), 0, 60);  
        } else {
            $petAttributes['price'] = null; // Optional: Explicitly set to null when not allowed
        }

        return $petAttributes; // Return the complete attributes array
    }
}
