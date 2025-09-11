<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Recreate feedback table with exactly the expected legacy columns.
     */
    public function up(): void
    {
        Schema::dropIfExists('feedback');

        Schema::create('feedback', function (Blueprint $table) {
            $table->bigIncrements('feedback_id');
            $table->unsignedBigInteger('from_user_id');
            $table->unsignedBigInteger('to_user_id');
            // Keep 'type' as a short string to match legacy usage (e.g., 'Trainer')
            $table->string('type', 32);
            $table->text('content');
            $table->unsignedTinyInteger('rate')->nullable();
            $table->boolean('is_visible')->default(true);
            // Only created_at is present in the legacy table
            $table->timestamp('created_at')->useCurrent();

            // Optional FKs (do not change columns). Comment out if undesired.
            $table->foreign('from_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('to_user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
        // Minimal restore to an empty shell (no data), in case of rollback
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
};
