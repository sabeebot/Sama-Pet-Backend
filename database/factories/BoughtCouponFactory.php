<?php

namespace Database\Factories;
use App\Models\Coupon;
use App\Models\petOwner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BoughtCoupon>
 */
class BoughtCouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
            return [
                'pet_owner_id' => PetOwner::factory(),   // Assuming you have a PetOwner model and factory
                'coupon_id' => Coupon::factory(),  // Assuming you have a Coupon model and factory
            ];
    }
}
