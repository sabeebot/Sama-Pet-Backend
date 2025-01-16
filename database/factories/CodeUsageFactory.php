<?php

namespace Database\Factories;

use App\Models\CodeUsage;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PetOwner;
use App\Models\Code;
class CodeUsageFactory extends Factory
{
    protected $model = CodeUsage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'owner_id' => PetOwner::factory(), 
            'date_of_usage' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'code_id' => Code::factory(), 
        ];
    }
}