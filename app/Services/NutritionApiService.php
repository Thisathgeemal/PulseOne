<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NutritionApiService
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        // You can use any nutrition API like Edamam, Spoonacular, or USDA FoodData Central
        $this->apiKey = env('NUTRITION_API_KEY', 'demo_key');
        $this->baseUrl = env('NUTRITION_API_URL', 'https://api.edamam.com/api/nutrition-details');
    }

    /**
     * Calculate nutrition from ingredients list
     */
    public function calculateNutrition(array $ingredients): array
    {
        try {
            // For demo purposes, we'll use a simple calculation
            // In production, replace this with actual API calls
            
            if (empty($ingredients)) {
                return $this->getDefaultNutrition();
            }

            // Simple ingredient-based calculation (replace with real API)
            $nutrition = $this->parseIngredientsToNutrition($ingredients);
            
            return [
                'success' => true,
                'data' => $nutrition
            ];

        } catch (\Exception $e) {
            Log::error('Nutrition API Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Unable to calculate nutrition automatically. Please enter values manually.',
                'data' => $this->getDefaultNutrition()
            ];
        }
    }

    /**
     * Parse ingredients and estimate nutrition (demo implementation)
     */
    private function parseIngredientsToNutrition(array $ingredients): array
    {
        $totalCalories = 0;
        $totalProtein = 0;
        $totalCarbs = 0;
        $totalFats = 0;
        $totalFiber = 0;
        $totalSugar = 0;
        $totalSodium = 0;

        // Simple ingredient database for demo
        $nutritionDatabase = $this->getIngredientDatabase();

        foreach ($ingredients as $ingredient) {
            $parsed = $this->parseIngredient($ingredient);
            $baseIngredient = $parsed['ingredient'];
            $quantity = $parsed['quantity'];

            // Find nutrition data for this ingredient
            $nutritionData = $this->findNutritionData($baseIngredient, $nutritionDatabase);
            
            if ($nutritionData) {
                $multiplier = $quantity / 100; // Normalize to 100g
                
                $totalCalories += $nutritionData['calories'] * $multiplier;
                $totalProtein += $nutritionData['protein'] * $multiplier;
                $totalCarbs += $nutritionData['carbs'] * $multiplier;
                $totalFats += $nutritionData['fats'] * $multiplier;
                $totalFiber += $nutritionData['fiber'] * $multiplier;
                $totalSugar += $nutritionData['sugar'] * $multiplier;
                $totalSodium += $nutritionData['sodium'] * $multiplier;
            }
        }

        return [
            'calories' => round($totalCalories, 1),
            'protein' => round($totalProtein, 1),
            'carbs' => round($totalCarbs, 1),
            'fats' => round($totalFats, 1),
            'fiber' => round($totalFiber, 1),
            'sugar' => round($totalSugar, 1),
            'sodium' => round($totalSodium, 1)
        ];
    }

    /**
     * Parse ingredient string to extract quantity and ingredient name
     */
    private function parseIngredient(string $ingredient): array
    {
        // Extract quantity and ingredient name from strings like "200g chicken breast"
        preg_match('/(\d+(?:\.\d+)?)\s*([a-zA-Z]*)\s+(.+)/', trim($ingredient), $matches);
        
        if (count($matches) >= 4) {
            $quantity = (float) $matches[1];
            $unit = strtolower($matches[2]);
            $ingredientName = strtolower(trim($matches[3]));
            
            // Convert to grams if needed
            if (in_array($unit, ['kg', 'kilogram', 'kilograms'])) {
                $quantity *= 1000;
            } elseif (in_array($unit, ['cup', 'cups'])) {
                $quantity *= 240; // Approximate conversion
            } elseif (in_array($unit, ['tbsp', 'tablespoon', 'tablespoons'])) {
                $quantity *= 15;
            } elseif (in_array($unit, ['tsp', 'teaspoon', 'teaspoons'])) {
                $quantity *= 5;
            }
            
            return [
                'quantity' => $quantity,
                'ingredient' => $ingredientName
            ];
        }
        
        // Default if parsing fails
        return [
            'quantity' => 100,
            'ingredient' => strtolower(trim($ingredient))
        ];
    }

    /**
     * Find nutrition data for an ingredient
     */
    private function findNutritionData(string $ingredient, array $database): ?array
    {
        foreach ($database as $key => $data) {
            if (strpos($ingredient, $key) !== false || strpos($key, $ingredient) !== false) {
                return $data;
            }
        }
        
        return null;
    }

    /**
     * Simple ingredient nutrition database (per 100g)
     * In production, this would come from a real API or comprehensive database
     */
    private function getIngredientDatabase(): array
    {
        return [
            'chicken breast' => ['calories' => 165, 'protein' => 31, 'carbs' => 0, 'fats' => 3.6, 'fiber' => 0, 'sugar' => 0, 'sodium' => 74],
            'chicken' => ['calories' => 165, 'protein' => 31, 'carbs' => 0, 'fats' => 3.6, 'fiber' => 0, 'sugar' => 0, 'sodium' => 74],
            'quinoa' => ['calories' => 368, 'protein' => 14.1, 'carbs' => 64.2, 'fats' => 6.1, 'fiber' => 7, 'sugar' => 0.9, 'sodium' => 5],
            'brown rice' => ['calories' => 123, 'protein' => 2.6, 'carbs' => 23, 'fats' => 0.9, 'fiber' => 1.8, 'sugar' => 0.4, 'sodium' => 5],
            'white rice' => ['calories' => 130, 'protein' => 2.7, 'carbs' => 28, 'fats' => 0.3, 'fiber' => 0.4, 'sugar' => 0.1, 'sodium' => 1],
            'salmon' => ['calories' => 208, 'protein' => 25.4, 'carbs' => 0, 'fats' => 12.4, 'fiber' => 0, 'sugar' => 0, 'sodium' => 86],
            'broccoli' => ['calories' => 34, 'protein' => 2.8, 'carbs' => 7, 'fats' => 0.4, 'fiber' => 2.6, 'sugar' => 1.5, 'sodium' => 33],
            'spinach' => ['calories' => 23, 'protein' => 2.9, 'carbs' => 3.6, 'fats' => 0.4, 'fiber' => 2.2, 'sugar' => 0.4, 'sodium' => 79],
            'sweet potato' => ['calories' => 86, 'protein' => 1.6, 'carbs' => 20.1, 'fats' => 0.1, 'fiber' => 3, 'sugar' => 4.2, 'sodium' => 6],
            'potato' => ['calories' => 77, 'protein' => 2, 'carbs' => 17, 'fats' => 0.1, 'fiber' => 2.2, 'sugar' => 0.8, 'sodium' => 6],
            'avocado' => ['calories' => 160, 'protein' => 2, 'carbs' => 8.5, 'fats' => 14.7, 'fiber' => 6.7, 'sugar' => 0.7, 'sodium' => 7],
            'banana' => ['calories' => 89, 'protein' => 1.1, 'carbs' => 22.8, 'fats' => 0.3, 'fiber' => 2.6, 'sugar' => 12.2, 'sodium' => 1],
            'apple' => ['calories' => 52, 'protein' => 0.3, 'carbs' => 13.8, 'fats' => 0.2, 'fiber' => 2.4, 'sugar' => 10.4, 'sodium' => 1],
            'egg' => ['calories' => 155, 'protein' => 13, 'carbs' => 1.1, 'fats' => 11, 'fiber' => 0, 'sugar' => 1.1, 'sodium' => 124],
            'tofu' => ['calories' => 76, 'protein' => 8, 'carbs' => 1.9, 'fats' => 4.8, 'fiber' => 0.3, 'sugar' => 0.6, 'sodium' => 7],
            'lentils' => ['calories' => 116, 'protein' => 9, 'carbs' => 20, 'fats' => 0.4, 'fiber' => 7.9, 'sugar' => 1.8, 'sodium' => 2],
            'chickpeas' => ['calories' => 164, 'protein' => 8.9, 'carbs' => 27.4, 'fats' => 2.6, 'fiber' => 7.6, 'sugar' => 4.8, 'sodium' => 7],
            'almonds' => ['calories' => 579, 'protein' => 21.2, 'carbs' => 21.6, 'fats' => 49.9, 'fiber' => 12.5, 'sugar' => 4.4, 'sodium' => 1],
            'walnuts' => ['calories' => 654, 'protein' => 15.2, 'carbs' => 13.7, 'fats' => 65.2, 'fiber' => 6.7, 'sugar' => 2.6, 'sodium' => 2],
            'olive oil' => ['calories' => 884, 'protein' => 0, 'carbs' => 0, 'fats' => 100, 'fiber' => 0, 'sugar' => 0, 'sodium' => 2],
            'yogurt' => ['calories' => 59, 'protein' => 10, 'carbs' => 3.6, 'fats' => 0.4, 'fiber' => 0, 'sugar' => 3.2, 'sodium' => 36],
            'milk' => ['calories' => 42, 'protein' => 3.4, 'carbs' => 5, 'fats' => 1, 'fiber' => 0, 'sugar' => 5, 'sodium' => 44],
            'cheese' => ['calories' => 113, 'protein' => 7, 'carbs' => 1, 'fats' => 9, 'fiber' => 0, 'sugar' => 1, 'sodium' => 215],
            'bread' => ['calories' => 265, 'protein' => 9, 'carbs' => 49, 'fats' => 3.2, 'fiber' => 2.7, 'sugar' => 5, 'sodium' => 491],
            'oats' => ['calories' => 389, 'protein' => 16.9, 'carbs' => 66.3, 'fats' => 6.9, 'fiber' => 10.6, 'sugar' => 0, 'sodium' => 2],
        ];
    }

    /**
     * Get default nutrition values
     */
    private function getDefaultNutrition(): array
    {
        return [
            'calories' => 250,
            'protein' => 20,
            'carbs' => 30,
            'fats' => 10,
            'fiber' => 5,
            'sugar' => 5,
            'sodium' => 300
        ];
    }

    /**
     * Call external nutrition API (example implementation)
     */
    private function callExternalNutritionApi(array $ingredients): array
    {
        // Example implementation for Edamam API
        /*
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '?app_id=' . env('EDAMAM_APP_ID') . '&app_key=' . $this->apiKey, [
            'title' => 'Recipe',
            'ingr' => $ingredients
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'calories' => $data['calories'] ?? 0,
                'protein' => $data['totalNutrients']['PROCNT']['quantity'] ?? 0,
                'carbs' => $data['totalNutrients']['CHOCDF']['quantity'] ?? 0,
                'fats' => $data['totalNutrients']['FAT']['quantity'] ?? 0,
                'fiber' => $data['totalNutrients']['FIBTG']['quantity'] ?? 0,
                'sugar' => $data['totalNutrients']['SUGAR']['quantity'] ?? 0,
                'sodium' => $data['totalNutrients']['NA']['quantity'] ?? 0,
            ];
        }
        */
        
        return $this->getDefaultNutrition();
    }
}
