<?php

namespace Database\Factories;

use App\Models\Reminder;
use App\Models\PetOwner;
use App\Models\Pet;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReminderFactory extends Factory
{
    protected $model = Reminder::class;

    public function definition()
    {
        return [
            'pet_owner_id' => PetOwner::factory(),
            'pet_id' => Pet::factory(), // Optional if pet_id is not required for all reminders
            'title' => $this->faker->sentence,
            'date' => $this->faker->date,
            'time' => $this->faker->time,
            'remind' => $this->faker->boolean,
            'repeat' => $this->faker->randomElement(['Doesn\'t Repeat', 'Daily', 'Weekly', 'Monthly']),
            'note' => $this->faker->text,
            'provider_id' => Provider::factory(),
        ];
    }
}