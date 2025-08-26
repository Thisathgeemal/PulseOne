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
        Schema::create('meal_compliances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dietplan_id')->index();
            $table->unsignedBigInteger('member_id')->index();
            $table->date('log_date');
            $table->json('meals_completed');
            $table->timestamps();

            $table->foreign('dietplan_id')->references('dietplan_id')->on('diet_plans')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_compliances');
    }
};
