<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('owner_id')->constrained('pet_owners')->onDelete('cascade');
            $table->dateTime('date_of_usage');
            $table->foreignId('coupon_id')->constrained()->onDelete('cascade');
            $table->timestamps(); 
            $table->softDeletes(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
    }
};
