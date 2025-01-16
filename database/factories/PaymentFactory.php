<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\PetOwner;
use App\Models\Order;
use App\Models\Provider;
use App\Models\Coupon;
use App\Models\Package;
use App\Models\Card;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        // Randomly decide whether to create a payment for an order, coupon, or package
        $paymentType = $this->faker->randomElement(['order', 'coupon', 'package']);

        $data = [
            'pet_owner_id' => PetOwner::factory(),  // Generate a pet owner
            'provider_id' => Provider::factory(),  // Generate a provider
            'card_id' => Card::factory(),  // Generate a card or leave null
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'bank_transfer']),  // Random payment method
            'amount' => $this->faker->randomFloat(2, 10, 1000),  // Random payment amount
            'discount_amount' => $this->faker->randomFloat(2, 0, 50),  // Random discount amount
            'currency' => 'USD',  // Default currency (USD)
            'transaction_id' => Str::random(10),  // Random transaction ID
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),  // Random payment status
            'description' => $this->faker->sentence(),  // Random description
            'payment_date' => now(),  // Payment date is now
            'metadata' => json_encode(['gateway' => 'Stripe', 'invoice_id' => $this->faker->uuid()]),  // Random metadata
        ];

        // Populate either order_id, coupon_id, or package_id based on the random selection
        if ($paymentType === 'order') {
            $data['order_id'] = Order::factory(); // Create an order if needed
        } elseif ($paymentType === 'coupon') {
            $data['coupon_id'] = Coupon::factory(); // Use an existing coupon
        } elseif ($paymentType === 'package') {
            $data['package_id'] = Package::factory(); // Use an existing package
        }

        return $data;
    }
}

