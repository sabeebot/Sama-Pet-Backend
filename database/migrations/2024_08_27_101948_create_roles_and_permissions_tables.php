<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Create the roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id(); // Unique ID for each role
            $table->string('name')->unique(); // Name of the role (e.g., admin, editor)
            $table->timestamps(); // Timestamps for created_at and updated_at
        });

        // Create the permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id(); // Unique ID for each permission
            $table->string('name')->unique(); // Name of the permission (e.g., create-post, edit-post)
            $table->timestamps(); // Timestamps for created_at and updated_at
        });

        // Create the role_admin table to link admins to roles
        Schema::create('role_admin', function (Blueprint $table) {
            $table->id(); // Unique ID for each record
            $table->foreignId('role_id')->constrained()->onDelete('cascade'); // Foreign key to roles table
            $table->foreignId('admin_id')->constrained()->onDelete('cascade'); // Foreign key to admins table
            $table->timestamps(); // Timestamps for created_at and updated_at
        });

        // Create the permission_role table to link roles to permissions
        Schema::create('permission_role', function (Blueprint $table) {
            $table->id(); // Unique ID for each record
            $table->foreignId('permission_id')->constrained()->onDelete('cascade'); // Foreign key to permissions table
            $table->foreignId('role_id')->constrained()->onDelete('cascade'); // Foreign key to roles table
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Drop the tables if the migration is rolled back
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_admin');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
