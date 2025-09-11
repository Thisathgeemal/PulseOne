<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('feedback')) return;

        // Drop existing FK if present, then change column to nullable, then re-add FK with SET NULL behavior
        Schema::table('feedback', function (Blueprint $table) {
            try { $table->dropForeign(['to_user_id']); } catch (\Throwable $e) { /* ignore if not present */ }
        });

        // Modify column to nullable using raw SQL to avoid doctrine/dbal dependency
        DB::statement('ALTER TABLE `feedback` MODIFY `to_user_id` BIGINT UNSIGNED NULL');

        Schema::table('feedback', function (Blueprint $table) {
            $table->foreign('to_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('feedback')) return;

        Schema::table('feedback', function (Blueprint $table) {
            try { $table->dropForeign(['to_user_id']); } catch (\Throwable $e) { /* ignore if not present */ }
        });

        DB::statement('ALTER TABLE `feedback` MODIFY `to_user_id` BIGINT UNSIGNED NOT NULL');

        Schema::table('feedback', function (Blueprint $table) {
            $table->foreign('to_user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
