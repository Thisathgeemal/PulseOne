<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->bigIncrements('meal_id');
            $table->string('meal_name');
            $table->string('description')->nullable();
            $table->string('image_path')->nullable();

            $table->decimal('calories_per_serving', 8, 2)->default(0);
            $table->decimal('protein_grams', 8, 2)->default(0);
            $table->decimal('carbs_grams', 8, 2)->default(0);
            $table->decimal('fats_grams', 8, 2)->default(0);
            $table->decimal('fiber_grams', 8, 2)->default(0);
            $table->decimal('sugar_grams', 8, 2)->default(0);
            $table->decimal('sodium_mg', 8, 2)->default(0);

            $table->decimal('serving_size', 8, 2)->default(1);
            $table->string('serving_unit')->default('serving');

            $table->text('dietary_tags')->nullable();
            $table->text('ingredients')->nullable();
            $table->text('preparation_method')->nullable();

            $table->integer('prep_time_minutes')->nullable();
            $table->integer('cook_time_minutes')->nullable();
            $table->integer('total_time_minutes')->nullable();
            $table->string('difficulty_level')->default('easy');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by_dietitian_id')->nullable()->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
