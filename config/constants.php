<?php

return [
    'roles' => [
        'admin' => 'Admin',
        'trainer' => 'Trainer',
        'member' => 'Member',
        'dietitian' => 'Dietitian',
    ],
    
    'user_statuses' => [
        'active' => 'active',
        'inactive' => 'inactive',
        'suspended' => 'suspended',
    ],
    
    'cache_keys' => [
        'leaderboard_prefix' => 'leaderboard',
        'user_data_prefix' => 'user_data',
    ],
    
    'api_endpoints' => [
        'weight_chart' => '/api/weight-chart/{dietPlanId}',
        'nutrition_api' => env('NUTRITION_API_URL', 'https://api.nutritionix.com'),
    ],
];
