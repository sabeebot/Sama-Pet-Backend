<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\Pet;
use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Membership>
 */
class MembershipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [];
    }

    public function fromPackage(Package $package, bool $useFirstPrice)
    {
        $startDate = fake()->dateTimeBetween('-1 year', 'now');
        $endDate = clone $startDate;

        if ($package->duration == 'monthly') {
            $endDate->modify('+1 month');
        } elseif ($package->duration == 'annually') {
            $endDate->modify('+1 year');
        }

        return $this->state(function () use ($startDate, $endDate, $package, $useFirstPrice) {
            return [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'price' => $useFirstPrice ? $package->price : $package->second_price,
            ];
        });
    }
}
