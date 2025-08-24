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

            // Relationships
            $table->unsignedBigInteger('trainer_id')->index();
            $table->unsignedBigInteger('member_id')->index();

            // Legacy date/time (kept for compatibility, but time is nullable now)
            $table->date('date')->nullable();
            $table->time('time')->nullable();

            $table->timestamp('start_at')->nullable(); // will later be NOT NULL after backfill

            // Duration + buffers
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->unsignedSmallInteger('buffer_before')->default(0);
            $table->unsignedSmallInteger('buffer_after')->default(10);

            // Extra metadata
            $table->text('description')->nullable();
            $table->enum('status', [
                'pending',
                'approved',
                'declined',
                'cancelled',
                'completed',
                'expired',
            ])->default('pending');

            $table->timestamp('hold_expires_at')->nullable();

            // Cancellation / decline info
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->string('decline_reason')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('trainer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cancelled_by')->references('id')->on('users')->nullOnDelete();

            // Indexes
            $table->index(['trainer_id', 'date', 'time'], 'idx_trainer_date_time');
            $table->index(['member_id', 'date'], 'idx_member_date');
            $table->index(['status'], 'idx_status');
            $table->index(['trainer_id', 'start_at'], 'idx_trainer_start_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }

};
