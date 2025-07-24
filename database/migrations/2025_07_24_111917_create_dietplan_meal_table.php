<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dietplan_meal', function (Blueprint $table) {
            $table->bigIncrements('dietplanmeal_id');

            $table->unsignedBigInteger('dietplan_id')->index();
            $table->unsignedBigInteger('meal_id')->index();
            $table->enum('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            $table->time('time');
            $table->decimal('quantity', 5, 2);
            $table->integer('calories');
            $table->integer('carbs');
            $table->integer('protein');
            $table->integer('fat');
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->foreign('dietplan_id')->references('dietplan_id')->on('diet_plans')->onDelete('cascade');
            $table->foreign('meal_id')->references('meal_id')->on('meals')->onDelete('cascade');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('dietplan_meal');
    }
};
