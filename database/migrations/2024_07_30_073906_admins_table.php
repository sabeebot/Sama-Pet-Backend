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
        Schema::dropIfExists('admins');
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('password', 256);
            $table->string('first_name', 32);
            $table->string('last_name', 32);
            $table->string('email', 64)
                ->unique();
            $table->string('role', 32);
            $table->enum('status', [
                'active',
                'non active',
                'cancelled',
            ]);
            $table->string('contact_number', 32)->nullable();
            $table->string('profile_image', 128)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
