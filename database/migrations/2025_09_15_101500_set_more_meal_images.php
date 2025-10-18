<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Map filenames to meals using tolerant LIKE matching so slight name differences won't block updates
        DB::table('meals')->where('meal_name', 'like', '%Quinoa%')->update(['image_path' => 'images/quinoa.jpg']);

        DB::table('meals')
            ->where('meal_name', 'like', '%Protein%')
            ->where('meal_name', 'like', '%Smoothie%')
            ->update(['image_path' => 'images/proteinsmoothie.webp']);

        DB::table('meals')->where('meal_name', 'like', '%Overnight%')->update(['image_path' => 'images/overnight.jpg']);

        DB::table('meals')
            ->where('meal_name', 'like', '%Grilled%')
            ->where('meal_name', 'like', '%Caesar%')
            ->where('meal_name', 'like', '%Salad%')
            ->update(['image_path' => 'images/GrilledChickenCaesarSalad.jpg']);

        DB::table('meals')
            ->where('meal_name', 'like', '%Greek%')
            ->where('meal_name', 'like', '%Parfait%')
            ->update(['image_path' => 'images/GreekYogurtParfait.jpg']);

        DB::table('meals')->where('meal_name', 'like', '%Egg Fried Rice%')->orWhere('meal_name', 'like', '%Fried Rice%')->update(['image_path' => 'images/eggfriedrice.jpg']);

        DB::table('meals')
            ->where('meal_name', 'like', '%Egg%')
            ->where('meal_name', 'like', '%Veggie%')
            ->where('meal_name', 'like', '%Toast%')
            ->update(['image_path' => 'images/Egg&VeggieScrambleToast.jpg']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove the values we set (only remove if they match the filenames we assigned)
        DB::table('meals')->where('image_path', 'images/quinoa.jpg')->update(['image_path' => null]);
        DB::table('meals')->where('image_path', 'images/proteinsmoothie.webp')->update(['image_path' => null]);
        DB::table('meals')->where('image_path', 'images/overnight.jpg')->update(['image_path' => null]);
        DB::table('meals')->where('image_path', 'images/GrilledChickenCaesarSalad.jpg')->update(['image_path' => null]);
        DB::table('meals')->where('image_path', 'images/GreekYogurtParfait.jpg')->update(['image_path' => null]);
        DB::table('meals')->where('image_path', 'images/eggfriedrice.jpg')->update(['image_path' => null]);
        DB::table('meals')->where('image_path', 'images/Egg&VeggieScrambleToast.jpg')->update(['image_path' => null]);
    }
};
