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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // Basic Info
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('username')->unique()->nullable();
            $table->string('password');
            $table->string('type')->default('user');
            // Custom Fields (IMPORTANT)
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('profile_image')->nullable();
            $table->text('address')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('marital_status')->nullable();
            // System Fields
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
                           
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
