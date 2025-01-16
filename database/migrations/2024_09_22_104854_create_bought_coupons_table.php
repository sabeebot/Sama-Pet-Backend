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
        Schema::create('bought_coupons', function (Blueprint $table) {
            $table->id();  
            $table->unsignedBigInteger('pet_owner_id');  
            $table->unsignedBigInteger('coupon_id');  
            $table->timestamps(); 
    
            $table->foreign('pet_owner_id')->references('id')->on('pet_owners')->onDelete('cascade');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bought_coupons');
    }
};
