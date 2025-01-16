<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('codes', function (Blueprint $table) {
            $table->id(); 
            $table->string('affiliate', 128); 
            $table->string('code', 64); 
            $table->dateTime('expiration_date'); 
            $table->integer('percentage'); 
            $table->timestamps(); 
            $table->softDeletes(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('codes'); 
    }
};