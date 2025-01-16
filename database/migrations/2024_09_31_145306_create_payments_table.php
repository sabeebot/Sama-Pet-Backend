<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();  // Auto-incrementing ID
            $table->unsignedBigInteger('pet_owner_id');  // Foreign key for the pet owner (payer)
            $table->unsignedBigInteger('provider_id')->nullable();  // Foreign key for the provider (nullable)
            $table->unsignedBigInteger('card_id')->nullable();  // Foreign key for the card used (nullable)
            $table->unsignedBigInteger('order_id')->nullable();  // Foreign key for the order (nullable)
            $table->unsignedBigInteger('coupon_id')->nullable();  // Foreign key for the coupon (nullable)
            $table->unsignedBigInteger('package_id')->nullable();  // Foreign key for the package (nullable)
            $table->string('payment_method');  // Payment method (e.g., credit card, PayPal, etc.)
            $table->decimal('amount', 10, 2);  // Payment amount (up to 999,999,999.99)
            $table->decimal('discount_amount', 10, 2)->default(0.00);  // Discount applied to the payment
            $table->string('currency', 3)->default('USD');  // Currency code (ISO 4217 format, like USD, EUR, etc.)
            $table->string('transaction_id')->nullable();  // Unique transaction ID from the payment gateway
            $table->string('status')->default('pending');  // Payment status (e.g., pending, completed, failed)
            $table->string('description')->nullable();  // Description or purpose of the payment
            $table->timestamp('payment_date')->nullable();  // Date when the payment was made
            $table->json('metadata')->nullable();  // Additional metadata (e.g., customer info, gateway response)

            $table->timestamps();  // Created_at and Updated_at timestamps
            $table->softDeletes();  // Soft delete support

            // Foreign key constraints
            $table->foreign('pet_owner_id')->references('id')->on('pet_owners')->onDelete('cascade');
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
            $table->foreign('card_id')->references('id')->on('cards')->onDelete('set null');  // Card used for payment
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');  // Order related to payment
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('set null');  // Coupon related to payment
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('set null');  // Package related to payment
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}