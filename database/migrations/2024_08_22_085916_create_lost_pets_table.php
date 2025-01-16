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
        Schema::create('lost_pets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('gender', ['m', 'f'])
                ->nullable();
            $table->string('name', 32);
            $table->string('pet_type', 32);
            $table->string('breed', 32);
            $table->string('color', 32);
            $table->string('image', 128)->nullable();
            $table->string('location', 128);
            $table->text('description'); // Allows longer text
            $table->foreignId('pet_owner_id')->constrained();
            $table->enum('role', ['Founder', 'Owner']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_pets');
    }
};
