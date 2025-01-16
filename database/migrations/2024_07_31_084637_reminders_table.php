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
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_owner_id')->constrained()->onDelete('cascade')->nullable();
            $table->foreignId('pet_id')->constrained()->onDelete('cascade')->nullable();
            $table->string('title');
            $table->date('date');
            $table->time('time');
            $table->boolean('remind');
            $table->enum('repeat', ["Doesn\'t Repeat", 'Daily', 'Weekly', 'Monthly', 'Annually']);
            $table->text('note')->nullable();
            $table->foreignId('provider_id')->constrained()->onDelete('cascade')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
