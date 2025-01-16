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
        Schema::table('providers', function (Blueprint $table) {
            // Change the 'profile_image' column to TEXT for longer image URLs
            $table->text('profile_image')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            // Rollback the change (set 'profile_image' back to string with 255 length)
            $table->string('profile_image', 255)->change();
        });
    }
};
