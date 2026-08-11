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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            // Relations
            $table->foreignId('user_patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('hospital_id')->nullable()->constrained('hospitals')->nullOnDelete();
            $table->foreignId('chamber_id')->nullable()->constrained('chambers')->cascadeOnDelete();
            $table->foreignId('doctor_schedule_id')->nullable()->constrained('doctor_schedules')->nullOnDelete();
            $table->decimal('consultation_fee', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->enum('appointment_type', ['HOSPITAL','CHAMBER','ONLINE'])->default('CHAMBER');
            // status 
            $table->enum('status', [
                'APPOINTED',
                'PRESCRIBED',
                'DELETED',
            ])->default('APPOINTED');
            $table->date('appointment_date');
            $table->time('appointment_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
