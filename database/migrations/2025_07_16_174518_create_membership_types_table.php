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
        Schema::create('membership_types', function (Blueprint $table) {
            $table->bigIncrements('type_id');
            $table->string('type_name');
            $table->integer('duration');
            $table->decimal('amount', 8, 2);
            $table->decimal('discount', 5, 2)->default(0);
            $table->decimal('price', 8, 2);
            $table->timestamp('created_at')->default(now());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_types');
    }
};
