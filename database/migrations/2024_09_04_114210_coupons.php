<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->timestamps(); 
            $table->softDeletes(); 
            $table->string('code', 64);
            $table->foreignId('provider_id')->constrained();
            $table->string('title', 255); 
            $table->string('image', 255); 
            $table->integer('quantity'); 
            $table->dateTime('expiration_date'); 
            $table->string('description', 256); 
            $table->boolean('membership')->default(false);
            $table->decimal('price', 4, 2); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons'); 
    }
};
