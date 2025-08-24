<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trainer_availabilities', function (Blueprint $t) {
            $t->id();
            $t->foreignId('trainer_id')->constrained('users')->cascadeOnDelete();
            $t->unsignedTinyInteger('weekday');   // 1=Mon ... 7=Sun
            $t->time('start_time');               // local time (HH:MM:SS)
            $t->time('end_time');
            $t->unsignedSmallInteger('slot_minutes')->default(60);
            $t->unsignedSmallInteger('buffer_minutes')->default(0);
            $t->timestamps();

            $t->unique(['trainer_id','weekday','start_time','end_time'], 'uniq_trainer_day_block');
            $t->index(['trainer_id','weekday'], 'idx_trainer_weekday');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_availabilities');
    }
};
