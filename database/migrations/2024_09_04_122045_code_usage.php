<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('code_usages', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('owner_id')->constrained;
            $table->dateTime('date_of_usage'); 
            $table->foreignId('code_id')->constrained;
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('code_usages'); 
    }
};