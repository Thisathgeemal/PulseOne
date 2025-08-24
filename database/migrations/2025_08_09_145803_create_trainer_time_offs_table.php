<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trainer_time_offs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('trainer_id')->constrained('users')->cascadeOnDelete();
            $t->date('date');                        // the specific day off/partial-off
            $t->time('start_time')->nullable();      // null means whole day
            $t->time('end_time')->nullable();        // null means whole day
            $t->string('reason')->nullable();
            $t->timestamps();

            $t->index(['trainer_id','date']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('trainer_time_offs');
    }
};
