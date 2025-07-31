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
        Schema::create('progress_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workoutplan_id');
            $table->unsignedBigInteger('user_id');
            $table->date('photo_date');
            $table->string('photo_path');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('workoutplan_id')->references('workoutplan_id')->on('workout_plans')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index('photo_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_photos');
    }
};
