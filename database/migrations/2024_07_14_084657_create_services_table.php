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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('title', 128);
            $table->string('short_description', 256);
            $table->float('old_price');
            $table->float('new_price')->nullable();
            $table->integer('percentage')->nullable();
            $table->string('image', 128)->nullable();
            $table->string('contact_number');
            $table->json('pet_type');
            $table->foreignId('provider_id')->constrained();
            $table->string('short_description_ar', 256);
            $table->string('title_ar', 128);
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
