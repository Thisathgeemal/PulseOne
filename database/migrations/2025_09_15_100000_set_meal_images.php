<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Note: image files should be placed under storage/app/public/meals/
    // Files are expected to be in public/images/
    DB::table('meals')->where('meal_name', 'Avocado Toast')->update(['image_path' => 'images/avacodo.jpeg']);
    DB::table('meals')->where('meal_name', 'Baked Salmon')->update(['image_path' => 'images/Best-Oven-Baked-Salmon-Recipe.jpg']);
    DB::table('meals')->where('meal_name', 'Baked Fish, Potatoes & Veg')->update(['image_path' => 'images/fishveg.jpeg']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    DB::table('meals')->where('meal_name', 'Avocado Toast')->update(['image_path' => null]);
    DB::table('meals')->where('meal_name', 'Baked Salmon')->update(['image_path' => null]);
    DB::table('meals')->where('meal_name', 'Baked Fish, Potatoes & Veg')->update(['image_path' => null]);
    }
};
