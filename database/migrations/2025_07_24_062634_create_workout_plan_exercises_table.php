<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('workout_plan_exercises', function (Blueprint $table) {
            $table->bigIncrements('planexercise_id');
            $table->unsignedBigInteger('workoutplan_id')->index();
            $table->unsignedBigInteger('exercise_id')->index();
            $table->integer('sets');
            $table->integer('reps');
            $table->integer('day_number');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('workoutplan_id')->references('workoutplan_id')->on('workout_plans')->onDelete('cascade');
            $table->foreign('exercise_id')->references('exercise_id')->on('exercises')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_plan_exercises');
    }
};
