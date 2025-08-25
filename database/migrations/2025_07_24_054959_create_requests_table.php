<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->bigIncrements('request_id');
            $table->unsignedBigInteger('member_id')->index();
            $table->unsignedBigInteger('trainer_id')->nullable()->index();
            $table->unsignedBigInteger('dietitian_id')->nullable()->index();

            $table->enum('plan_type', ['Basic', 'Intermediate', 'Advanced'])->nullable();
            $table->date('preferred_start_date')->nullable();
            $table->string('available_days')->nullable();

            $table->string('goal')->nullable();
            $table->decimal('current_weight', 5, 2)->nullable();
            $table->decimal('target_weight', 5, 2)->nullable();
            $table->string('timeframe')->nullable();
            $table->integer('meals_per_day')->default(3);

            $table->string('description')->nullable();
            $table->enum('type', ['Workout', 'Diet']);
            $table->enum('status', ['Pending', 'In Progress', 'Approved', 'Rejected'])->default('Pending');
            $table->timestamps();

            $table->foreign('member_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('trainer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('dietitian_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
