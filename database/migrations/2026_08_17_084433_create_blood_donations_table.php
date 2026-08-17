<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blood_donations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('donor_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('patient_name');            
            $table->enum('patient_gender', ['Male', 'Female', 'Other'])->nullable();

            $table->text('patient_disease')->nullable();
            $table->string('patient_blood_group', 10)->nullable();

            $table->date('donation_date');

            $table->string('hospital_name')->nullable();
            $table->string('hospital_address')->nullable();

            $table->unsignedInteger('units')->default(1);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('donor_user_id');
            $table->index('donation_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_donations');
    }
};