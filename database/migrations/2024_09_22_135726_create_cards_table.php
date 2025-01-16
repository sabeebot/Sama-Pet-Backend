<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();  // Auto-incrementing ID
            $table->unsignedBigInteger('provider_id')->nullable();  // Foreign key for the provider (nullable)
            $table->unsignedBigInteger('pet_owner_id')->nullable();  // Foreign key for the pet owner (nullable)
            $table->string('card_number', 20);  // Card number (up to 20 digits)
            $table->string('cardholder_name');  // Name of the cardholder
            $table->date('expiration_date');  // Expiration date of the card (e.g., 2025-12-31)
            $table->string('card_type')->nullable();  // Card type (e.g., Visa, MasterCard)
            $table->string('cvv', 4);  // CVV number (up to 4 digits for American Express, 3 digits otherwise)
            $table->boolean('is_default')->default(false);  // Whether this is the default card for the user
            $table->timestamps();  // Created_at and Updated_at timestamps
            $table->softDeletes();  // Soft delete support
    
            // Foreign key constraints
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
            $table->foreign('pet_owner_id')->references('id')->on('pet_owners')->onDelete('cascade');
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
