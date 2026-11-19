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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->integer('item_id')->nullable();
            $table->integer('cat_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('complaint_id')->nullable();
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->string('complaint_image')->nullable();
            $table->integer('complaint_status')->nullable();
            $table->integer('status_change_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
