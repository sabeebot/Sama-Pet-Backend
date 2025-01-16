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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->boolean('is_free_trial');
            $table->string('description', 256);
            $table->string('title', 64);
            $table->decimal(
                column: 'price',
                total: 4,
                places: 2,
                unsigned: true,
            );
            $table->string('duration', 64);
            $table->decimal(
                column: 'second_price',
                total: 4,
                places: 2,
                unsigned: true,
            );
            $table->boolean('status');
        });

        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->decimal(
                column: 'price',
                total: 4,
                places: 2,
                unsigned: true,
            );

            // $table->timestamp('start_date',precision:0);
            // $table->timestamp('end_date',precision:0);

            $table->datetime('start_date');
            $table->datetime('end_date');


            $table->foreignId('package_id');
            $table->foreignId('pet_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
        Schema::dropIfExists('memberships');
    }
};
