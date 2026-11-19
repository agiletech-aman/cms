<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('complaint_visits')) {
            Schema::create('complaint_visits', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('complaint_id')->index();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('comment_id')->nullable();
                $table->dateTime('visit_date');
                $table->text('remark')->nullable();
                $table->timestamps();
            });

            // Backfill: existing single visit_date of each complaint becomes its first visit
            DB::statement("
                INSERT INTO complaint_visits (complaint_id, user_id, visit_date, created_at, updated_at)
                SELECT id, status_change_by, visit_date, NOW(), NOW()
                FROM complaints
                WHERE visit_date IS NOT NULL
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_visits');
    }
};
