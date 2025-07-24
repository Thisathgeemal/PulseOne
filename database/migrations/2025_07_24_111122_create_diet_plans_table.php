<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diet_plans', function (Blueprint $table) {
            $table->bigIncrements('dietplan_id');
            $table->unsignedBigInteger('dietitian_id')->index();
            $table->unsignedBigInteger('member_id')->index();
            $table->string('plan_name');
            $table->date('start_date');
            $table->date('end_date');
            $table->dateTime('created_at');

            $table->foreign('dietitian_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diet_plans');
    }
};
