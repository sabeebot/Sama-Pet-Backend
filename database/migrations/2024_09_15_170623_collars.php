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
        Schema::create('collars', function (Blueprint $table) {
            $table->id(); 
            $table->string('url'); 
            $table->foreignId('pet_id')->nullable()->constrained('pets')->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null'); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collars');
    }
};
