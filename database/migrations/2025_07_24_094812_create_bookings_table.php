<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('booking_id');
            $table->unsignedBigInteger('trainer_id')->index();
            $table->unsignedBigInteger('member_id')->index();
            $table->dateTime('date');
            $table->time('time');
            $table->enum('mode', ['Online', 'In-Gym']);
            $table->text('description')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Declined', 'Done'])->default('Pending');
            $table->timestamps();

            $table->foreign('trainer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('users')->onDelete('cascade');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
