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
        if (!Schema::hasColumn('doctor_schedules', 'consultation_fee')) {
            Schema::table('doctor_schedules', function (Blueprint $table) {
                $table->decimal('consultation_fee', 10, 2)->nullable()->after('max_patients');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('doctor_schedules', 'consultation_fee')) {
            Schema::table('doctor_schedules', function (Blueprint $table) {
                $table->dropColumn('consultation_fee');
            });
        }
    }
};
