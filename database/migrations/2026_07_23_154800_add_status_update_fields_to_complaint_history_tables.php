<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('comments', 'remark')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->text('remark')->nullable()->after('comment');
            });
        }

        if (!Schema::hasColumn('complaint_images', 'comment_id')) {
            Schema::table('complaint_images', function (Blueprint $table) {
                $table->unsignedBigInteger('comment_id')->nullable()->after('user_id');
            });
        }

        if (!Schema::hasColumn('old_comments', 'remark')) {
            Schema::table('old_comments', function (Blueprint $table) {
                $table->text('remark')->nullable()->after('comment');
            });
        }

        if (!Schema::hasColumn('old_complaint_images', 'comment_id')) {
            Schema::table('old_complaint_images', function (Blueprint $table) {
                $table->unsignedBigInteger('comment_id')->nullable()->after('user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('comments', 'remark')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropColumn('remark');
            });
        }

        if (Schema::hasColumn('complaint_images', 'comment_id')) {
            Schema::table('complaint_images', function (Blueprint $table) {
                $table->dropColumn('comment_id');
            });
        }

        if (Schema::hasColumn('old_comments', 'remark')) {
            Schema::table('old_comments', function (Blueprint $table) {
                $table->dropColumn('remark');
            });
        }

        if (Schema::hasColumn('old_complaint_images', 'comment_id')) {
            Schema::table('old_complaint_images', function (Blueprint $table) {
                $table->dropColumn('comment_id');
            });
        }
    }
};
