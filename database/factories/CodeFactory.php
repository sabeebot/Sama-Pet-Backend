<?php

namespace Database\Factories;

use App\Models\Code;
use Illuminate\Database\Eloquent\Factories\Factory;

class CodeFactory extends Factory
{
    protected $model = Code::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'affiliate' => $this->faker->word(),
            'code' => $this->faker->unique()->word(),
            'expiration_date' => $this->faker->dateTimeBetween('now', '+5 years'),
            'percentage' => $this->faker->numberBetween(1, 100),
        ];
    }
}