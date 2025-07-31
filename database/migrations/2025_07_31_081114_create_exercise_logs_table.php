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
        Schema::create('exercise_logs', function (Blueprint $table) {
            $table->bigIncrements('log_id');
            $table->unsignedBigInteger('workoutplan_id');
            $table->unsignedBigInteger('exercise_id');
            $table->unsignedBigInteger('member_id');
            $table->date('log_date');
            $table->integer('sets_completed')->nullable();
            $table->integer('reps_completed')->nullable();
            $table->float('weight')->nullable();
            $table->timestamps();

            $table->foreign('workoutplan_id')->references('workoutplan_id')->on('workout_plans')->onDelete('cascade');
            $table->foreign('exercise_id')->references('exercise_id')->on('exercises')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_logs');
    }
};
