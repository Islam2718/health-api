<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $tables = [
        'jobs',
        'failed_jobs',
        'personal_access_tokens',
        'users',
        'otps',
        'doctors',
        'educations',
        'professional_experiences',
        'chambers',
        'doctor_schedules',
        'hospitals',
        'appointments',
        'appointment_prescriptions',
        'medicine_companies',
        'medicines',
        'posts',
        'comments',
        'post_ratings',
        'blood_donations',
        'ambulances',
        'stores',
        'store_products',
        'stocks',
        'medicine_category',
        'orders',
        'order_items',
    ];

    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        foreach ($this->tables as $table) {
            DB::statement(
                "ALTER TABLE `{$table}` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT"
            );
        }
    }

    public function down(): void
    {
        // Keep existing auto-increment settings during rollback.
    }
};