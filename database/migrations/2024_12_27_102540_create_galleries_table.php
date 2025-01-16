<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
       
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade'); // Foreign key to 'providers' table
            $table->string('type_en', 128);
            $table->string('type_ar', 128);
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->json('image_url')->nullable();
            $table->longText('banner')->nullable();
            $table->longText('contract')->nullable();
            $table->longText('document')->nullable();
            $table->timestamps(); // Adds created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
