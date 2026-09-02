<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('payment_method')->default('CASH')->change();
        });

        if (Schema::hasColumn('orders', 'contact_phone')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('contact_phone');
            });
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('contact_phone')->nullable();
            $table->string('payment_method')->nullable()->default(null)->change();
        });
    }
};
