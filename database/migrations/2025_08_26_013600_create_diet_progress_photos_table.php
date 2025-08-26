<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('diet_progress_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dietplan_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('photo_path');
            $table->date('photo_date');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('dietplan_id')->references('dietplan_id')->on('diet_plans')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

    }

    public function down()
    {
        Schema::dropIfExists('diet_progress_photos');
    }
};
