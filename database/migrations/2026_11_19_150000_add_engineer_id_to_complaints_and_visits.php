<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('complaints', 'engineer_id')) {
            Schema::table('complaints', function (Blueprint $table) {
                $table->unsignedBigInteger('engineer_id')->nullable()->after('root_cause');
            });
        }

        if (!Schema::hasColumn('complaint_visits', 'engineer_id')) {
            Schema::table('complaint_visits', function (Blueprint $table) {
                $table->unsignedBigInteger('engineer_id')->nullable()->after('user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('complaints', 'engineer_id')) {
            Schema::table('complaints', function (Blueprint $table) {
                $table->dropColumn('engineer_id');
            });
        }

        if (Schema::hasColumn('complaint_visits', 'engineer_id')) {
            Schema::table('complaint_visits', function (Blueprint $table) {
                $table->dropColumn('engineer_id');
            });
        }
    }
};
