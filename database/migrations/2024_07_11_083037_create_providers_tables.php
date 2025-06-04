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
        Schema::dropIfExists('veterinarians');
        Schema::dropIfExists('trainer_info');
        Schema::dropIfExists('doctor_info');
    
        // Now drop the 'providers' table
        Schema::dropIfExists('providers');

        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('type', 255); // Increased length of 'type' column
            
            // Removed default value for 'availability' as MySQL doesn't support default values for JSON
            $table->json('availability');
            $table->string('name', 64);
            $table->string('email', 64)->unique();
            $table->string('office', 128)->nullable();
            $table->string('road', 128);
            $table->string('block', 128);
            $table->string('city', 128);
            $table->string('address', 255)->nullable();
            $table->string('contact_no', 64);
            $table->string('profile_image', 128)->nullable();
            $table->string('timing', 128)->nullable();
            $table->string('status')->default('pending'); // 'pending', 'approved', 'rejected'
        
            $table->json('social_media');
            $table->json('documents');
        
            // New Columns from the Profile Interface
            $table->string('provider_name_en', 128);
            $table->string('provider_name_ar', 128);
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->string('cr_number', 64);
            $table->string('website')->nullable();
            $table->string('instagram')->nullable();
            $table->json('availability_days')->nullable(); // Store availability days as JSON
            $table->json('availability_hours')->nullable(); // Store start and end times as JSON
            $table->json('authorized_persons')->nullable();
        });

        // Doctor Info Table Migration
        Schema::create('doctor_info', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('provider_id')->constrained()->onDelete('cascade');

            // Existing Columns
            $table->unsignedTinyInteger('years_of_experience');
            $table->string('medical_degree_and_specializtion', 128);

            // Additional Columns from the Picture
            $table->string('availbiltyDay')->nullable();
            $table->string('contantEng')->nullable();
            $table->string('contentAra')->nullable();
            $table->string('educationEng')->nullable();
            $table->string('educationAra')->nullable();
            $table->string('filterDate')->nullable();
            $table->string('filterTime')->nullable();
            $table->longText('imageUrl')->nullable();  
            $table->string('introAra')->nullable();
            $table->string('introEng')->nullable();
            $table->string('nameAra')->nullable();
            $table->string('nameEng')->nullable();
            $table->unsignedTinyInteger('noOfYearAra')->nullable();
            $table->unsignedTinyInteger('noOfYearEng')->nullable();
            $table->string('status');
        });

        // Extra trainer info
        Schema::create('trainer_info', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('provider_id')->constrained()->onDelete('cascade');

            $table->unsignedTinyInteger('years_of_experience');
            $table->string('medical_degree_and_specializtion', 128);
        });

        // Vets belonging to a clinic
        Schema::create('veterinarians', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade');
            $table->string('name', 64);
            $table->string('email', 64)->unique();
            $table->string('bio', 256);
            $table->string('education', 64);

            $table->unsignedTinyInteger('years_of_experience');

            $table->string('picture', 128);
            $table->string('specialization', 128);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veterinarians');
        Schema::dropIfExists('trainer_info');
        Schema::dropIfExists('doctor_info');
    }
};
