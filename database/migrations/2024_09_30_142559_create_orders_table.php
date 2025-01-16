<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();  // Auto-incrementing ID
            $table->unsignedBigInteger('pet_owner_id');  // Foreign key for the user
            $table->timestamp('order_date');  // Date when the order was placed
            $table->decimal('amount', 10, 2);  // Total order amount
            $table->decimal('discount_amount', 10, 2)->default(0.00);  // Discount applied to the order
            $table->json('metadata')->nullable();  // Additional metadata (product info)
            $table->string('status')->default('pending'); 
            $table->timestamps();  // Created_at and Updated_at timestamps
            $table->softDeletes();  // Soft delete support

            // Foreign key constraints
            $table->foreign('pet_owner_id')->references('id')->on('pet_owners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

