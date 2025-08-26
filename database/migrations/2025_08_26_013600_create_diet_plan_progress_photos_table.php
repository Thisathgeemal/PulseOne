<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('diet_plan_progress_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dietplan_id');
            $table->unsignedBigInteger('member_id');
            $table->date('photo_date');
            $table->string('photo_path');
            $table->string('note', 1000)->nullable();
            $table->timestamps();

            $table->foreign('dietplan_id')->references('dietplan_id')->on('diet_plans')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('diet_plan_progress_photos');
    }
};
