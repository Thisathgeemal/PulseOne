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
        // Update Avocado Toast
        DB::table('meals')
            ->where('meal_name', 'like', '%Avocado%')
            ->update(['image_path' => 'images/avocadotoast.jpg']);

        // Update Baked Fish variations -> CheesyBakedFish.jpg
        DB::table('meals')
            ->where('meal_name', 'like', '%Baked Fish%')
            ->orWhere('meal_name', 'like', '%Fish%Potatoes%')
            ->update(['image_path' => 'images/CheesyBakedFish.jpg']);

        // Update Protein Smoothie
        DB::table('meals')
            ->where('meal_name', 'like', '%Smoothie%')
            ->update(['image_path' => 'images/blueberryproteinsmoothie.jpg']);

        // Update Grilled Chicken Caesar Salad
        DB::table('meals')
            ->where('meal_name', 'like', '%Grill%Chicken%')
            ->orWhere('meal_name', 'like', '%Caesar%')
            ->update(['image_path' => 'images/GrilledChickenCaesarSalad.jpg']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('meals')
            ->where('image_path', 'images/avocadotoast.jpg')
            ->update(['image_path' => null]);

        DB::table('meals')
            ->where('image_path', 'images/CheesyBakedFish.jpg')
            ->update(['image_path' => null]);

        DB::table('meals')
            ->where('image_path', 'images/blueberryproteinsmoothie.jpg')
            ->update(['image_path' => null]);

        DB::table('meals')
            ->where('image_path', 'images/GrilledChickenCaesarSalad.jpg')
            ->update(['image_path' => null]);
    }
};
