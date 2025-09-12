<?php

return [
    'cache_duration' => env('LEADERBOARD_CACHE_DURATION', 300), // 5 minutes default
    'default_period_days' => env('LEADERBOARD_DEFAULT_DAYS', 7),
    'monthly_period_days' => env('LEADERBOARD_MONTHLY_DAYS', 29),
    'max_custom_period_days' => env('LEADERBOARD_MAX_CUSTOM_DAYS', 90),
    'max_entries' => env('LEADERBOARD_MAX_ENTRIES', 50),
];
