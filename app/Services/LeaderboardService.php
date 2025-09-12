<?php
namespace App\Services;

use App\Models\DailyWorkoutLog;
use App\Models\ExerciseLog;
use App\Models\MealCompliance;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class LeaderboardService
{
    /**
     * Compute leaderboard score for a user in a period.
     * Returns associative array with score, pct and raw counts used for tie-breakers.
     */
    public function computeForUser(int $userId, Carbon $start, Carbon $end): array
    {
        // Attendance days (use Attendance model if exists, fallback to DailyWorkoutLog days)
        $attendanceDays = Attendance::where('user_id', $userId)
            ->whereBetween('check_in_time', [$start, $end])
            ->selectRaw('DATE(check_in_time) as d')
            ->groupBy('d')
            ->get()
            ->count();

        if ($attendanceDays === 0) {
            // fallback to daily workout logs (some setups store check-ins elsewhere)
            $attendanceDays = DailyWorkoutLog::where('member_id', $userId)
                ->whereBetween('log_date', [$start->toDateString(), $end->toDateString()])
                ->distinct('log_date')
                ->count('log_date');
        }

    // Target days = inclusive days in window (use date-only diff to avoid time boundary issues)
    $startDate = Carbon::parse($start->toDateString());
    $endDate = Carbon::parse($end->toDateString());
    $targetDays = max(1, $startDate->diffInDays($endDate) + 1);
        $attendancePct = ($attendanceDays / $targetDays) * 100;

        // Prefer stored completion_percentage average if available (more accurate)
        $avgCompletion = DailyWorkoutLog::where('member_id', $userId)
            ->whereBetween('log_date', [$start->toDateString(), $end->toDateString()])
            ->avg('completion_percentage');

        // Workouts completed = distinct days with a workout log (fallback)
        $workoutsCompleted = DailyWorkoutLog::where('member_id', $userId)
            ->whereBetween('log_date', [$start->toDateString(), $end->toDateString()])
            ->distinct('log_date')
            ->count('log_date');

        if ($avgCompletion !== null) {
            $workoutPct = min(100, round($avgCompletion, 2));
        } else {
            $workoutPct = $targetDays ? min(100, round(($workoutsCompleted / $targetDays) * 100, 2)) : 0;
        }

        // Diet compliance: count compliant meals across period
        $mealRows = MealCompliance::where('member_id', $userId)
            ->whereBetween('log_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $compliantMeals = 0;
        foreach ($mealRows as $mr) {
            $mc = $mr->meals_completed;
            if (is_string($mc)) {
                $decoded = json_decode($mc, true);
                if (is_array($decoded)) {
                    $mc = $decoded;
                }
            }
            if (is_array($mc)) {
                // count truthy meal entries (breakfast/lunch/dinner/snack keys)
                foreach ($mc as $v) {
                    $compliantMeals += $v ? 1 : 0;
                }
            } elseif (is_numeric($mc)) {
                $compliantMeals += (int) $mc;
            }
        }

        $mealsPerDay = 4; // adjust if your app uses different
        $maxMeals = $targetDays * $mealsPerDay;
        $dietPct = $maxMeals ? min(100, round(($compliantMeals / $maxMeals) * 100, 2)) : 0;

        // Bonuses (small, capped) to break ties and reward volume
        $bonusAttendance = min($attendanceDays * 0.2, 6);     // max +6 pts
        $bonusWorkouts   = min($workoutsCompleted * 0.15, 5); // max +5 pts
        $bonusDiet       = min($compliantMeals * 0.1, 4);     // max +4 pts

        // Weights (tweakable)
        $wAtt = 0.3; $wWork = 0.4; $wDiet = 0.3;

        $base = $attendancePct * $wAtt + $workoutPct * $wWork + $dietPct * $wDiet;
        $score = round($base + $bonusAttendance + $bonusWorkouts + $bonusDiet, 2);

        // include legacy keys expected by existing views
        return [
            'score' => $score,
            'attendance_days' => (int) $attendanceDays,
            'attendances' => (int) $attendanceDays, // legacy alias used in views
            'workouts_completed' => (int) $workoutsCompleted,
            'workout_days' => (int) $workoutsCompleted, // legacy alias
            'compliant_meals' => (int) $compliantMeals,
            'compliance_pct' => round($dietPct, 2), // legacy alias used in views
            'attendance_pct' => round($attendancePct, 2),
            'workout_pct' => round($workoutPct, 2),
            'diet_pct' => round($dietPct, 2),
        ];
    }

    /**
     * Build leaderboard for a list of user ids for the period.
     */
    public function buildForUserIds(array $userIds, Carbon $start, Carbon $end, int $limit = 50): array
    {
        $rows = [];
        $users = User::whereIn('id', $userIds)->where('show_in_leaderboard', true)->get()->keyBy('id');

        foreach ($userIds as $uid) {
            if (! isset($users[$uid])) continue;
            $computed = $this->computeForUser($uid, $start, $end);
            $rows[] = array_merge(['user' => $users[$uid], 'user_id' => $uid], $computed);
        }

        usort($rows, function ($a, $b) {
            if ($a['score'] !== $b['score']) return $b['score'] <=> $a['score'];
            if ($a['attendance_days'] !== $b['attendance_days']) return $b['attendance_days'] <=> $a['attendance_days'];
            if ($a['workouts_completed'] !== $b['workouts_completed']) return $b['workouts_completed'] <=> $a['workouts_completed'];
            if ($a['compliant_meals'] !== $b['compliant_meals']) return $b['compliant_meals'] <=> $a['compliant_meals'];
            return $a['user']->created_at <=> $b['user']->created_at;
        });

        // limit results
        $rows = array_slice($rows, 0, $limit);

        // Provide a tiny, deterministic display offset so scores shown in the UI are unique
        // without affecting the sorting (we already sorted). This helps prevent many
        // identical-looking scores when users have equal base computations.
        $n = count($rows);
        for ($i = 0; $i < $n; $i++) {
            // larger offset for higher-placed rows so display matches ranking
            $offset = ($n - $i) * 0.001; // visible at 3 decimal places but very small
            // keep original score for logic but add a separate display value
            $rows[$i]['score_display'] = round($rows[$i]['score'] + $offset, 3);
        }

        return $rows;
    }
}
