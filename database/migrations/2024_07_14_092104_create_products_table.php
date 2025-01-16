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
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('products');
        
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            // Add the provider_id column as an unsignedBigInteger
            $table->unsignedBigInteger('provider_id')->nullable();
            
            // Set up the foreign key constraint
            $table->foreign('provider_id')
                  ->references('id')
                  ->on('providers') // Replace 'providers' with the name of the provider table
                  ->onDelete('cascade'); // Optional: handles deletion cascade
            
            // Add other columns
            $table->string('name');
            $table->unsignedInteger('total_stock');
            $table->string('selected_category');
            $table->string('selected_subcategory');
            $table->string('description');
            $table->string('status');
            $table->float('price')->nullable();
            $table->boolean('availability')->nullable();
            $table->unsignedInteger('total_sold')->nullable();
            $table->longText('image_url');
            $table->timestamps();
            $table->softDeletes();
        });
    


        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
        
            // Existing columns
            $table->string('name', 128);
            $table->float('old_price');
            $table->float('new_price')->nullable();
            $table->integer('quantity');
            $table->integer('percentage')->nullable();
            $table->string('contact_number', 20);
            $table->json('pet_type');
            $table->foreignId('provider_id');
            $table->foreign('provider_id')
                ->references('id')
                ->on('providers')
                ->onDelete('cascade');
                $table->foreignId('category_id');
                $table->foreign('category_id')
                ->references('id')
                ->on('product_categories')
                ->onDelete('cascade');
        
            // New columns from the image
            $table->string('product_name_en');
            $table->string('product_description_en');
            $table->string('product_description_ar');
            $table->unsignedInteger('amount');
            $table->unsignedInteger('discount');
            $table->string('status');
            $table->longText('image_url')->nullable();
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('products');
    }
};
