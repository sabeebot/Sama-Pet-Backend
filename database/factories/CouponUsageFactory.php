<?php

namespace Database\Factories;

use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PetOwner;

class CouponUsageFactory extends Factory
{
    protected $model = CouponUsage::class;

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
            'coupon_id' => Coupon::factory(), 
        ];
    }
}