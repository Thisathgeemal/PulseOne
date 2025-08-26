<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Diet Plan Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 10px;
            padding: 0;
        }
        .page-container {
            border: 3px solid #000000; 
            padding: 20px;
            min-height: 900px;
            position: relative; 
        }
        header {
            display: flex;
            align-items: center;
            justify-content: start;
            background-color: #a10000;
            color: white;
            padding: 10px 20px;
            border-bottom: 2px solid #4a0000;
            height: 40px; 
        }
        header h1 {
            font-size: 24px;
            margin: 0;
            text-align: center;
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px; 
        }
        th, td {
            padding: 8px;
            border: 1px solid #333;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
        }
        h2 {
            text-align: center;
            color: #000000;
            margin-top: 25px; 
        }
        .report-info {
            margin: 0px;
            color: #333;
        }
        .nutrition-summary {
            background-color: #f9f9f9;
            padding: 15px;
            border: 2px solid #ddd;
            margin: 20px 0;
            border-radius: 5px;
        }
        .nutrition-targets {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .target-item {
            text-align: center;
            flex: 1;
        }
        footer {
            position: absolute;
            bottom: 10px;
            width: calc(100% - 40px); 
            border-top: 2px solid #4a0000;
            padding-top: 5px;
            color: #333;
            font-size: 12px;
            text-align: center;
        }
        .meal-section {
            margin-bottom: 25px;
        }
        .meal-title {
            background-color: #4a0000;
            color: white;
            padding: 8px 15px;
            margin: 0;
            border-radius: 5px 5px 0 0;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <header>
            <h1>PULSEONE</h1>
        </header>

        <h2>Diet Plan Report - {{ $plan->plan_name }}</h2>

        <div class="report-info">
            <p><strong>Date:</strong> {{ $date }}</p>
            <p><strong>Member:</strong> 
                {{ trim(($plan->member->first_name ?? '') . ' ' . ($plan->member->last_name ?? '')) ?: 'N/A' }}
            </p>
            <p><strong>Dietitian:</strong> 
                {{ trim(($plan->dietitian->first_name ?? '') . ' ' . ($plan->dietitian->last_name ?? '')) ?: 'N/A' }}
            </p>    
            <p><strong>Duration:</strong> {{ \Carbon\Carbon::parse($plan->start_date)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($plan->end_date)->format('d-m-Y') }}</p>
            <p><strong>Status:</strong> {{ ucfirst($plan->status) }}</p>
        </div>

        <!-- Nutrition Targets Summary -->
        <div class="nutrition-summary">
            <h3 style="margin-top: 0; color: #4a0000;">Daily Nutrition Targets</h3>
            <div class="nutrition-targets">
                <div class="target-item">
                    <strong>{{ $plan->daily_calories_target ?? 'N/A' }}</strong><br>
                    <small>Calories</small>
                </div>
                <div class="target-item">
                    <strong>{{ $plan->daily_protein_target ?? 'N/A' }}g</strong><br>
                    <small>Protein</small>
                </div>
                <div class="target-item">
                    <strong>{{ $plan->daily_carbs_target ?? 'N/A' }}g</strong><br>
                    <small>Carbs</small>
                </div>
                <div class="target-item">
                    <strong>{{ $plan->daily_fats_target ?? 'N/A' }}g</strong><br>
                    <small>Fats</small>
                </div>
            </div>
        </div>

        <hr>

        @if($plan->dietPlanMeals && $plan->dietPlanMeals->count() > 0)
            @php
                $groupedMeals = $plan->dietPlanMeals->groupBy(function($meal) {
                    // Group by meal type (breakfast, lunch, dinner, snack)
                    $time = $meal->time;
                    if (strpos($time, '08:') === 0 || strpos($time, '07:') === 0) return 'Breakfast';
                    if (strpos($time, '12:') === 0 || strpos($time, '13:') === 0) return 'Lunch';
                    if (strpos($time, '18:') === 0 || strpos($time, '19:') === 0 || strpos($time, '20:') === 0) return 'Dinner';
                    return 'Snack';
                });
            @endphp

            @foreach($groupedMeals as $mealType => $meals)
                <div class="meal-section">
                    <h3 class="meal-title">{{ $mealType }}</h3>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Meal</th>
                                <th>Time</th>
                                <th>Calories</th>
                                <th>Protein (g)</th>
                                <th>Carbs (g)</th>
                                <th>Fat (g)</th>
                                <th>Quantity</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($meals as $mealPlan)
                            <tr>
                                <td>{{ $mealPlan->meal ? $mealPlan->meal->description : 'Custom Meal' }}</td>
                                <td>{{ \Carbon\Carbon::parse($mealPlan->time)->format('H:i') }}</td>
                                <td>{{ $mealPlan->calories ?? 'N/A' }}</td>
                                <td>{{ $mealPlan->protein ?? 'N/A' }}</td>
                                <td>{{ $mealPlan->carbs ?? 'N/A' }}</td>
                                <td>{{ $mealPlan->fat ?? 'N/A' }}</td>
                                <td>{{ $mealPlan->quantity ?? '1' }}</td>
                                <td>{{ $mealPlan->notes ?: 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @else
            <div style="text-align: center; padding: 40px; color: #666;">
                <h3>No meals have been assigned to this diet plan yet.</h3>
                <p>The dietitian will add specific meals and schedule soon.</p>
            </div>
        @endif

        @if($plan->plan_description)
            <div style="margin-top: 30px;">
                <h3 style="color: #4a0000;">Plan Description</h3>
                <p style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #4a0000;">
                    {{ $plan->plan_description }}
                </p>
            </div>
        @endif

        @if($plan->dietitian_instructions)
            <div style="margin-top: 20px;">
                <h3 style="color: #4a0000;">Dietitian Instructions</h3>
                <p style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #4a0000;">
                    {{ $plan->dietitian_instructions }}
                </p>
            </div>
        @endif

        <footer>
            <p>Generated by PULSEONE Diet Management System | {{ $date }}</p>
        </footer>
    </div>
</body>
</html>
