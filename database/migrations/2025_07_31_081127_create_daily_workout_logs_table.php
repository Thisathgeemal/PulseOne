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
        Schema::create('daily_workout_logs', function (Blueprint $table) {
            $table->bigIncrements('dailylog_id');
            $table->unsignedBigInteger('workoutplan_id');
            $table->unsignedBigInteger('member_id');
            $table->date('log_date');
            $table->integer('completed_exercises')->default(0);
            $table->integer('total_exercises')->default(0);
            $table->float('completion_percentage')->default(0);
            $table->integer('workout_duration')->default(0)->comment('Duration in minutes');
            $table->timestamps();

            $table->foreign('workoutplan_id')->references('workoutplan_id')->on('workout_plans')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_workout_logs');
    }
};
