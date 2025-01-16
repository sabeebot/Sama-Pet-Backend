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
        Schema::create('pet_owners', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('password', 256);
            $table->string('first_name', 32);
            $table->string('last_name', 32);
            $table->enum('gender', ['m', 'f']);
            $table->string('phone', 32);
            $table->string('email', 64)->unique();
            $table->string('nationality', 64);
            $table->string('profile_image', 128)->nullable();
            $table->string('location', 32);
            $table->string('city', 32);
            $table->date('date_of_birth');
            $table->string('house')->nullable();
            $table->string('road')->nullable();
            $table->string('block')->nullable();
            $table->string('building_name')->nullable();
            $table->string('apt_number')->nullable();
            $table->string('floor')->nullable();
            $table->string('company')->nullable();
            $table->string('status')->nullable();
        });


        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->enum('gender', ['m', 'f'])
                ->nullable();

            $table->string('name', 32);

            $table->date('age');

            $table->decimal(
                column: 'weight',
                total: 3,
                places: 1,
                unsigned: true,
            );
            $table->decimal(
                column: 'height',
                total: 4,
                places: 1,
                unsigned: true,
            );

            $table->string('pet_type', 32);
            $table->string('breed', 32);

            $table->string('color', 32);
            $table->string('image', 128)->nullable();

            $table->string('is_vaccinated', 15);
            $table->string('is_microchipped', 15);
            $table->string('is_neutered', 15);
            $table->boolean('is_lost')->nullable();
            $table->boolean('allow_selling')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('description', 1000)->nullable();
            $table->boolean('allow_adoption')->nullable();
            $table->json('documents')->nullable();

            $table->foreignId('pet_owner_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
        Schema::dropIfExists('pet_owners');
    }
};
