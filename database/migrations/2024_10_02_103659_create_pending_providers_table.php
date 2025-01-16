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
        Schema::create('pending_providers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name', 64);
            $table->string('email', 64)->unique();
            $table->string('password');
            $table->string('contact_no', 64);
            $table->string('address', 128);
            $table->enum('type', ['doctor', 'pet shop', 'groomer', 'pet clinic', 'trainer']);
            $table->string('status')->default('pending'); // Status field to track approval
            $table->string('token')->unique()->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_providers');
    }
};
