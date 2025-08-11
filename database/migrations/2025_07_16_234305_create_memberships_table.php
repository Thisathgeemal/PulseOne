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
        Schema::create('memberships', function (Blueprint $table) {
            $table->bigIncrements('membership_id');
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('type_id')->index();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['Pending', 'Active', 'Expired', 'Cancelled'])->default('Active');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('type_id')->references('type_id')->on('membership_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
