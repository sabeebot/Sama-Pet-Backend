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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['ready_to_launch', 'custom_ad_design']);
            $table->string('ad_image')->nullable();
            $table->string('logo_image')->nullable();
            $table->string('business_image')->nullable();
            $table->string('business_name')->nullable();
            $table->text('ad_description')->nullable();
            $table->string('phone_number')->nullable();
            $table->json('social_media')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
