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
        Schema::create('ambulances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('brand_model')->nullable();
            $table->string('license_plate_number')->unique();
            $table->string('phone_number');
            $table->enum('ambulance_type', ['AC', 'NonAC', 'AIR', 'Freeze'])->default('NonAC')->nullable();
            $table->json('equipment_list')->nullable();
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->index(['user_id', 'is_active']);
            $table->index('ambulance_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ambulances');
    }
};
