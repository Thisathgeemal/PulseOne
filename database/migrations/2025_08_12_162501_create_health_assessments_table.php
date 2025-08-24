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
        Schema::create('health_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->onDelete('cascade');

            // Basic Information
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->decimal('height_cm', 5, 2)->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();

            // Activity & Goals
            $table->enum('activity_level', ['sedentary', 'lightly_active', 'moderately_active', 'very_active', 'extra_active'])->nullable();
            $table->json('fitness_goals')->nullable(); // weight_loss, muscle_gain, endurance, strength, etc.

                                                            // Medical Information
            $table->json('medical_conditions')->nullable(); // diabetes, hypertension, heart_disease, etc.
            $table->json('medications')->nullable();
            $table->json('injuries_surgeries')->nullable();
            $table->json('allergies')->nullable();

            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            $table->json('emergency_contacts')->nullable();

            // Exercise Information
            $table->enum('exercise_experience', ['beginner', 'intermediate', 'advanced'])->nullable();
            $table->enum('preferred_workout_time', ['morning', 'afternoon', 'evening', 'flexible'])->nullable();
            $table->text('workout_limitations')->nullable();

                                                              // Health & Lifestyle
            $table->json('dietary_restrictions')->nullable(); // vegetarian, vegan, gluten_free, etc.
            $table->enum('smoking_status', ['never', 'former', 'current'])->nullable();
            $table->enum('alcohol_consumption', ['never', 'rarely', 'occasionally', 'regularly'])->nullable();
            $table->integer('sleep_hours')->nullable();
            $table->integer('stress_level')->nullable(); // 1-10 scale

            // Medical Clearance
            $table->boolean('doctor_clearance')->nullable();
            $table->json('par_q_responses')->nullable(); // PAR-Q+ questionnaire responses

            // Additional
            $table->text('additional_notes')->nullable();

            // Completion tracking
            $table->boolean('is_complete')->default(false);
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['member_id', 'is_complete']);
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_assessments');
    }
};
