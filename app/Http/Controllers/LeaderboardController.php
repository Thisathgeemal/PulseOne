<?php
namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\DailyWorkoutLog;
use App\Models\MealCompliance;
use App\Services\LeaderboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LeaderboardController extends Controller
{
    // Unified endpoint: ?period=weekly|monthly|custom&semantics=calendar&start=YYYY-MM-DD&end=YYYY-MM-DD
    public function index(Request $request)
    {
        $now       = Carbon::now();
        $period    = strtolower((string) $request->query('period', 'weekly'));
        $semantics = strtolower((string) $request->query('semantics', ''));

        // Determine window
        if ($period === 'monthly') {
            // Default to rolling 30-day window for monthly; use semantics=calendar for calendar month
            if ($semantics === 'calendar') {
                $start = (clone $now)->startOfMonth();
                $end   = $now;
            } else {
                $end   = $now;
                $start = (clone $end)->subDays(29)->startOfDay(); // rolling 30 days
            }
        } elseif ($period === 'last-week') {
            // Previous calendar week (Mon–Sun)
            $currWeekStart = (clone $now)->startOfWeek(\Carbon\Carbon::MONDAY);
            $start         = (clone $currWeekStart)->subWeek();
            $end           = (clone $currWeekStart)->subDay()->endOfDay();
        } elseif ($period === 'custom') {
            try {
                $startStr = (string) $request->query('start');
                $endStr   = (string) $request->query('end');
                $start    = Carbon::parse($startStr)->startOfDay();
                $end      = Carbon::parse($endStr)->endOfDay();
                if ($start->gt($end)) {
                    throw new \InvalidArgumentException('Start after end');
                }
                // Optional safety: cap at 90 days
                if ($start->diffInDays($end) > 90) {
                    $start = (clone $end)->subDays(90)->startOfDay();
                }
            } catch (\Throwable $e) {
                // Fallback to weekly on parse errors
                $end    = $now;
                $start  = (clone $end)->subDays(6)->startOfDay();
                $period = 'weekly';
            }
        } else { // weekly default
            $end    = $now;
            $start  = (clone $end)->subDays(6)->startOfDay(); // rolling 7 days
            $period = 'weekly';
        }

        // Cache key includes period and window
        $cacheKey = sprintf('leaderboard.%s.%s.%s', $period, $start->format('Ymd'), $end->format('Ymd'));

        $cached = Cache::get($cacheKey);
        if ($cached) {
            if (! empty($cached['entries']) && is_array($cached['entries'])) {
                $n = count($cached['entries']);
                for ($i = 0; $i < $n; $i++) {
                    if (! isset($cached['entries'][$i]['score_display']) && isset($cached['entries'][$i]['score'])) {
                        $offset                                 = ($n - $i) * 0.001;
                        $cached['entries'][$i]['score_display'] = round($cached['entries'][$i]['score'] + $offset, 3);
                    }
                }
            }
            return view('memberDashboard.leaderboard', array_merge($cached, ['start' => $start, 'end' => $end]));
        }

        // collect candidate user ids from attendance/workout/meal tables
        $attendanceRows = Attendance::whereBetween('check_in_time', [$start, $end])
            ->selectRaw('user_id')
            ->groupBy('user_id')
            ->get()
            ->pluck('user_id')
            ->toArray();

        $workoutRows = DailyWorkoutLog::whereBetween('log_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('member_id')
            ->groupBy('member_id')
            ->get()
            ->pluck('member_id')
            ->toArray();

        $complianceRows = MealCompliance::whereBetween('log_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('member_id')
            ->groupBy('member_id')
            ->get()
            ->pluck('member_id')
            ->toArray();

        $userIds = collect($attendanceRows)
            ->merge($workoutRows)
            ->merge($complianceRows)
            ->unique()
            ->filter()
            ->values()
            ->all();

        if (empty($userIds)) {
            return view('memberDashboard.leaderboard', [
                'entries' => [],
                'start'   => $start,
                'end'     => $end,
            ]);
        }

        $service = new LeaderboardService();
        $entries = $service->buildForUserIds($userIds, $start, $end, 50);

        $payload = ['entries' => $entries];
        Cache::put($cacheKey, $payload, 300);

        return view('memberDashboard.leaderboard', array_merge($payload, ['start' => $start, 'end' => $end]));
    }
    // Weekly consistency leaderboard (on-the-fly)
    public function weekly(Request $request)
    {
        $end   = Carbon::now();
        $start = (clone $end)->subDays(6)->startOfDay(); // 7-day window

        // Cache key for weekly leaderboard
        $cacheKey = 'leaderboard.weekly.' . $start->format('Ymd') . '.' . $end->format('Ymd');

        $cached = Cache::get($cacheKey);
        if ($cached) {
            // ensure cached entries include the UI-only score_display (added by service in new code)
            if (! empty($cached['entries']) && is_array($cached['entries'])) {
                $n = count($cached['entries']);
                for ($i = 0; $i < $n; $i++) {
                    if (! isset($cached['entries'][$i]['score_display']) && isset($cached['entries'][$i]['score'])) {
                        $offset                                 = ($n - $i) * 0.001;
                        $cached['entries'][$i]['score_display'] = round($cached['entries'][$i]['score'] + $offset, 3);
                    }
                }
            }

            return view('memberDashboard.leaderboard', array_merge($cached, ['start' => $start, 'end' => $end]));
        }

        // collect candidate user ids from attendance/workout/meal tables
        $attendanceRows = Attendance::whereBetween('check_in_time', [$start, $end])
            ->selectRaw('user_id')
            ->groupBy('user_id')
            ->get()
            ->pluck('user_id')
            ->toArray();

        $workoutRows = DailyWorkoutLog::whereBetween('log_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('member_id')
            ->groupBy('member_id')
            ->get()
            ->pluck('member_id')
            ->toArray();

        $complianceRows = MealCompliance::whereBetween('log_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('member_id')
            ->groupBy('member_id')
            ->get()
            ->pluck('member_id')
            ->toArray();

        $userIds = collect($attendanceRows)
            ->merge($workoutRows)
            ->merge($complianceRows)
            ->unique()
            ->filter()
            ->values()
            ->all();

        if (empty($userIds)) {
            return view('memberDashboard.leaderboard', [
                'entries' => [],
                'start'   => $start,
                'end'     => $end,
            ]);
        }

        $service = new LeaderboardService();
        $entries = $service->buildForUserIds($userIds, $start, $end, 50);

        $payload = ['entries' => $entries];
        Cache::put($cacheKey, $payload, 300);

        return view('memberDashboard.leaderboard', array_merge($payload, ['start' => $start, 'end' => $end]));
    }

    // Monthly consistency leaderboard (current calendar month)
    public function monthly(Request $request)
    {
        $end   = Carbon::now();
        $start = (clone $end)->startOfMonth();

        // Cache key for monthly leaderboard
        $cacheKey = 'leaderboard.monthly.' . $start->format('Ymd') . '.' . $end->format('Ymd');

        $cached = Cache::get($cacheKey);
        if ($cached) {
            if (! empty($cached['entries']) && is_array($cached['entries'])) {
                $n = count($cached['entries']);
                for ($i = 0; $i < $n; $i++) {
                    if (! isset($cached['entries'][$i]['score_display']) && isset($cached['entries'][$i]['score'])) {
                        $offset                                 = ($n - $i) * 0.001;
                        $cached['entries'][$i]['score_display'] = round($cached['entries'][$i]['score'] + $offset, 3);
                    }
                }
            }

            return view('memberDashboard.leaderboard', array_merge($cached, ['start' => $start, 'end' => $end]));
        }

        // collect candidate user ids from attendance/workout/meal tables for the month
        $attendanceRows = Attendance::whereBetween('check_in_time', [$start, $end])
            ->selectRaw('user_id')
            ->groupBy('user_id')
            ->get()
            ->pluck('user_id')
            ->toArray();

        $workoutRows = DailyWorkoutLog::whereBetween('log_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('member_id')
            ->groupBy('member_id')
            ->get()
            ->pluck('member_id')
            ->toArray();

        $complianceRows = MealCompliance::whereBetween('log_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('member_id')
            ->groupBy('member_id')
            ->get()
            ->pluck('member_id')
            ->toArray();

        $userIds = collect($attendanceRows)
            ->merge($workoutRows)
            ->merge($complianceRows)
            ->unique()
            ->filter()
            ->values()
            ->all();

        if (empty($userIds)) {
            return view('memberDashboard.leaderboard', [
                'entries' => [],
                'start'   => $start,
                'end'     => $end,
            ]);
        }

        $service = new LeaderboardService();
        $entries = $service->buildForUserIds($userIds, $start, $end, 50);

        $payload = ['entries' => $entries];
        Cache::put($cacheKey, $payload, 300);

        return view('memberDashboard.leaderboard', array_merge($payload, ['start' => $start, 'end' => $end]));
    }
}
