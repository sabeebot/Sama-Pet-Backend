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
        // Schema::table('providers', function (Blueprint $table) {
        //     $table->string('name')->nullable()->change();
        //     $table->string('address')->nullable()->change();
        //     $table->string('contact_no')->nullable()->change();
        //     $table->json('social_media')->nullable()->change();
        //     $table->boolean('is_profile_completed')->default(false);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('providers', function (Blueprint $table) {
        //     $table->string('name')->nullable(false)->change();
        //     $table->string('address')->nullable(false)->change();
        //     $table->string('contact_no')->nullable(false)->change();
        //     $table->json('social_media')->nullable(false)->change();
        //     $table->dropColumn('is_profile_completed');
        // });
    }
};
