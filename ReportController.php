<?php
namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Booking;
use App\Models\DietPlan;
use App\Models\Feedback;
use App\Models\HealthAssessment;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\Payment;
use App\Models\Request as SessionRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkoutPlan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    // Format date for reports
    protected function getFormattedDate($datetimeInput)
    {
        return Carbon::parse($datetimeInput)->format('Y-m-d');
    }

    // Abort if required parameters are missing
    protected function abortIfMissing($params)
    {
        foreach ($params as $param) {
            if (! $param) {
                abort(400, 'Missing required parameters.');
            }
        }
    }

    // User detail report
    public function generateUserReport(Request $request)
    {
        $datetimeInput = $request->input('datetime');
        $role          = $request->input('role');
        $this->abortIfMissing([$datetimeInput, $role]);

        $formattedDate = $this->getFormattedDate($datetimeInput);

        $users = User::whereHas('roles', fn($q) => $q->where('role_name', $role))->get();

        $pdf = Pdf::loadView('report.userReport', compact('formattedDate', 'role', 'users'));

        return $pdf->download("{$role}_Report.pdf");
    }

    // Role detail report
    public function generateRoleReport(Request $request)
    {
        $datetimeInput = $request->input('datetime');
        $this->abortIfMissing([$datetimeInput]);

        $formattedDate = $this->getFormattedDate($datetimeInput);

        $roles = Role::withCount('users')->get();

        $pdf = Pdf::loadView('report.roleReport', compact('formattedDate', 'roles'));

        return $pdf->download("Role_Report.pdf");
    }

    // Membership detail report
    public function generateMembershipReport(Request $request)
    {
        $datetimeInput = $request->input('datetime');
        $this->abortIfMissing([$datetimeInput]);

        $formattedDate  = $this->getFormattedDate($datetimeInput);
        $memberships    = Membership::all();
        $membershipType = MembershipType::all();

        $pdf = Pdf::loadView('report.membershipReport', compact('formattedDate', 'memberships', 'membershipType'));

        return $pdf->download("Membership_Report.pdf");
    }

    // Membertype detail report
    public function generateMembertypeReport(Request $request)
    {
        $datetimeInput = $request->input('datetime');
        $this->abortIfMissing([$datetimeInput]);

        $formattedDate = $this->getFormattedDate($datetimeInput);
        $memberTypes   = MembershipType::all();

        $pdf = Pdf::loadView('report.membertypeReport', compact('formattedDate', 'memberTypes'));

        return $pdf->download("Membershiptype_Report.pdf");
    }

    // Workout plan report
    public function generateWorkoutReport($id)
    {
        $user = Auth::user();

        $plan = WorkoutPlan::with([
            'member',
            'trainer',
            'workoutPlanExercises.exercise',
        ])
            ->where(function ($query) use ($user) {
                $query->where('trainer_id', $user->id)
                    ->orWhere('member_id', $user->id);
            })
            ->findOrFail($id);

        $groupedExercises = $plan->workoutPlanExercises->groupBy('day_number');
        $date             = now()->format('Y-m-d');

        $pdf = Pdf::loadView('report.workoutplanReport', compact('plan', 'groupedExercises', 'date'));

        return $pdf->download("WorkoutPlan_Report_{$plan->plan_name}.pdf");
    }

    // Attendance report
    public function generateAttendanceReport()
    {
        $date = now()->format('Y-m-d');

        $attendances = Attendance::with('user.roles')
            ->orderBy('check_in_time', 'desc')
            ->get();

        $pdf = Pdf::loadView('report.attendanceReport', compact('attendances', 'date'));

        return $pdf->download("Attendance_Records_{$date}.pdf");
    }

    // Payment report
    public function generatePaymentReport(Request $request)
    {
        $datetimeInput = $request->input('datetime');
        $this->abortIfMissing([$datetimeInput]);

        $formattedDate = $this->getFormattedDate($datetimeInput);

        $payments = Payment::with(['user', 'membershipType'])->get();

        $pdf = Pdf::loadView('report.paymentReport', compact('formattedDate', 'payments'));

        return $pdf->download("Payment_Report.pdf");
    }

    // Payment report for logged-in user
    public function generateMemberPaymentReport(Request $request)
    {
        $datetimeInput = $request->input('datetime');
        $this->abortIfMissing([$datetimeInput]);

        $formattedDate = $this->getFormattedDate($datetimeInput);
        $userId        = Auth::id();

        $payments = Payment::with(['user', 'membershipType'])
            ->where('user_id', $userId)
            ->get();

        $pdf = Pdf::loadView('report.memberPaymentReport', compact('formattedDate', 'payments'));

        return $pdf->download("Member_Payment_Report.pdf");
    }

    // Membership report for logged-in user
    public function generateMemberMembershipReport(Request $request)
    {
        $datetimeInput = $request->input('datetime');
        $this->abortIfMissing([$datetimeInput]);

        $formattedDate  = $this->getFormattedDate($datetimeInput);
        $membershipType = MembershipType::all();
        $userId         = Auth::id();

        $memberships = Membership::with(['user', 'membershipType'])
            ->where('user_id', $userId)
            ->get();

        $pdf = Pdf::loadView('report.memberMembershipReport', compact('formattedDate', 'memberships', 'membershipType'));

        return $pdf->download("Member_Membership_Report.pdf");
    }

    // Generate member health report
    public function generateMemberHealthReport($memberId)
    {
        $this->abortIfMissing([$memberId]);

        // Retrieve completed health assessments for the given member
        $assessment = HealthAssessment::where('member_id', $memberId)
            ->with('member')
            ->where('is_complete', true)
            ->first();

        if (! $assessment) {
            abort(404, 'No completed health assessments found for this member');
        }

        // Generate the PDF
        $pdf = Pdf::loadView('report.healthReport', compact('assessment'));

        $member   = $assessment->member;
        $fileName = 'Member_Health_Report_' . $member->first_name . '_' . $member->last_name . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($fileName);
    }

    // Diet plan report
    public function generateDietReport(DietPlan $dietPlan)
    {
        $this->abortIfMissing([$dietPlan]);

        $dietPlan->load([
            'member',
            'dietitian',
            'dietPlanMeals.meal',
        ]);

        $date = Carbon::now()->format('Y-m-d');

        $pdf = Pdf::loadView('report.dietplanReport', [
            'plan' => $dietPlan,
            'date' => $date,
        ]);

        return $pdf->download("DietPlan_Report_{$dietPlan->plan_name}.pdf");
    }

    // Get report view
    public function getReportView()
    {
        return view('adminDashboard.report');
    }

    // Get the Monthly Revenue
    public function monthlyRevenue(Request $request)
    {
        $month = $request->query('month', now()->month);
        $year  = $request->query('year', now()->year);

        $payments = Payment::whereYear('payment_date', $year)
            ->whereMonth('payment_date', $month)
            ->get();

        // Group by day
        $dailyRevenue = $payments->groupBy(function ($payment) {
            return $payment->payment_date->format('Y-m-d');
        })->map(function ($dayPayments) {
            return $dayPayments->sum('amount');
        });

        // Stats
        $totalIncome = $payments->sum('amount');
        $daysCount   = $dailyRevenue->count() ?: 1;
        $dailyAvg    = round($totalIncome / $daysCount);
        $bestDay     = $dailyRevenue->sortDesc()->keys()->first() ?? '-';

        $topMembership = $payments->groupBy('type_id')
            ->map(fn($p) => $p->sum('amount'))
            ->sortDesc()
            ->keys()
            ->first();
        $topMembershipName = $topMembership
            ? MembershipType::find($topMembership)->type_name
            : '-';

        $preferredPayment = $payments->groupBy('payment_method')
            ->map(fn($p) => $p->sum('amount'))
            ->sortDesc()
            ->keys()
            ->first() ?? '-';

        return response()->json([
            'dailyRevenue'  => $dailyRevenue,
            'totalIncome'   => $totalIncome,
            'dailyAvg'      => $dailyAvg,
            'bestDay'       => $bestDay,
            'topMembership' => $topMembershipName,
            'paymentMethod' => $preferredPayment,
        ]);
    }

    // Get the User Details
    public function getUserAnalytics(Request $request)
    {
        $year  = $request->query('year', now()->year);
        $month = $request->query('month', now()->month);

        // Total, Active, and Inactive users up to the selected month
        $totalUsers = User::whereYear('created_at', '<=', $year)
            ->whereMonth('created_at', '<=', $month)
            ->count();

        $activeUsers = User::where('is_active', true)
            ->whereYear('created_at', '<=', $year)
            ->whereMonth('created_at', '<=', $month)
            ->count();

        $inactiveUsers = User::where('is_active', false)
            ->whereYear('created_at', '<=', $year)
            ->whereMonth('created_at', '<=', $month)
            ->count();

        // New users in the selected month
        $newUsers = User::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        // Most registered date in the selected month
        $mostRegisteredDateRecord = User::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy('date')
            ->orderByDesc('total')
            ->first();

        $mostRegisteredDate = $mostRegisteredDateRecord
            ? date('Y-m-d', strtotime($mostRegisteredDateRecord->date))
            : '-';

        // Roles count up to the selected month (cumulative)
        $rolesCount = [
            'admins'     => User::where('is_active', true)
                ->whereHas('roles', fn($q) => $q->where('role_name', 'Admin'))
                ->whereYear('created_at', '<=', $year)
                ->whereMonth('created_at', '<=', $month)
                ->count(),
            'members'    => User::where('is_active', true)
                ->whereHas('roles', fn($q) => $q->where('role_name', 'Member'))
                ->whereYear('created_at', '<=', $year)
                ->whereMonth('created_at', '<=', $month)
                ->count(),
            'trainers'   => User::where('is_active', true)
                ->whereHas('roles', fn($q) => $q->where('role_name', 'Trainer'))
                ->whereYear('created_at', '<=', $year)
                ->whereMonth('created_at', '<=', $month)
                ->count(),
            'dietitians' => User::where('is_active', true)
                ->whereHas('roles', fn($q) => $q->where('role_name', 'Dietitian'))
                ->whereYear('created_at', '<=', $year)
                ->whereMonth('created_at', '<=', $month)
                ->count(),
        ];

        // User Growth Over Time (cumulative totals by month in selected year)
        $monthlyGrowth = [];
        for ($m = 1; $m <= 12; $m++) {
            $count = User::where('is_active', true)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', '<=', $m)
                ->count();

            $monthlyGrowth[] = [
                'month' => $m,
                'total' => $count,
            ];
        }

        return response()->json([
            'totalUsers'         => $totalUsers,
            'activeUsers'        => $activeUsers,
            'inactiveUsers'      => $inactiveUsers,
            'newUsers'           => $newUsers,
            'mostRegisteredDate' => $mostRegisteredDate,
            'rolesCount'         => $rolesCount,
            'monthlyGrowth'      => $monthlyGrowth,
        ]);
    }

    // Get the Feedback Details
    public function getMonthlyFeedback(Request $request)
    {
        // Get year and month from request or default to current
        $year  = $request->query('year', now()->year);
        $month = $request->query('month', now()->month);

        // Filter feedbacks for the given month
        $feedbacks = Feedback::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('is_visible', true)
            ->get();

        // Calculate KPIs
        $totalFeedbacks   = $feedbacks->count();
        $avgRating        = $totalFeedbacks ? round($feedbacks->avg('rate'), 1) : 0;
        $positiveFeedback = $feedbacks->where('rate', '>=', 3)->count();
        $negativeFeedback = $feedbacks->where('rate', '<', 3)->count();

        // Most mentioned type
        $mostMentionedType = $feedbacks->groupBy('type')
            ->sortByDesc(fn($group) => count($group))
            ->keys()
            ->first() ?? '-';

        // Star Rating Breakdown (1⭐ → 5⭐)
        $starBreakdown = [];
        for ($i = 1; $i <= 5; $i++) {
            $starBreakdown[] = $feedbacks->where('rate', $i)->count();
        }

        // Monthly trend (we can return daily averages)
        $daysInMonth  = Carbon::create($year, $month)->daysInMonth;
        $dailyRatings = [];
        $labels       = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date         = Carbon::create($year, $month, $day);
            $dayFeedbacks = $feedbacks->where('created_at', '>=', $date->startOfDay())
                ->where('created_at', '<=', $date->endOfDay());
            $dailyRatings[] = $dayFeedbacks->count() ? round($dayFeedbacks->avg('rate'), 1) : 0;
            $labels[]       = $date->format('d M');
        }

        return response()->json([
            'kpis'   => [
                'avgRating'         => $avgRating,
                'totalFeedbacks'    => $totalFeedbacks,
                'positiveFeedbacks' => $positiveFeedback,
                'negativeFeedbacks' => $negativeFeedback,
                'mostMentionedType' => $mostMentionedType,
            ],
            'charts' => [
                'dailyLabels'   => $labels,
                'dailyRatings'  => $dailyRatings,
                'starBreakdown' => $starBreakdown,
            ],
        ]);
    }

    // Get the Sessions Details
    public function getMonthlySessions(Request $request)
    {
        $year  = $request->query('year', now()->year);
        $month = $request->query('month', now()->month);

        // Assign Diet Plans (completed) for the month
        $assignDietPlanCount = SessionRequest::where('type', 'diet')
            ->where('status', 'completed')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        // Assign Workout Plans (completed) for the month
        $assignWorkoutPlanCount = SessionRequest::where('type', 'workout')
            ->where('status', 'completed')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        // Pending Requests (request + booking) for the month
        $pendingRequestsFromRequest = SessionRequest::where('status', 'pending')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $pendingRequestsFromBooking = Booking::where('status', 'pending')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $pendingRequests = $pendingRequestsFromRequest + $pendingRequestsFromBooking;

        // Completed sessions for the month
        $completedSessions = Booking::where('status', 'completed')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        // Upcoming sessions (approved) starting in the month
        $upcomingSessions = Booking::where('status', 'approved')
            ->whereYear('start_at', $year)
            ->whereMonth('start_at', $month)
            ->count();

        // Monthly Trend (last 30 days within the requested month)
        $dates         = collect();
        $dietCounts    = collect();
        $workoutCounts = collect();
        $bookingCounts = collect();

        // Determine the first and last day of the month
        $startOfMonth = Carbon::createFromDate($year, $month, 1);
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $dates->push($date->format('d M'));

            $dietCounts->push(SessionRequest::where('type', 'diet')
                    ->where('status', 'completed')
                    ->whereDate('created_at', $date)
                    ->count());

            $workoutCounts->push(SessionRequest::where('type', 'workout')
                    ->where('status', 'completed')
                    ->whereDate('created_at', $date)
                    ->count());

            $bookingCounts->push(Booking::where('status', 'completed')
                    ->whereDate('created_at', $date)
                    ->count());
        }

        $sessionTypeData = [
            'dietPlans'         => $assignDietPlanCount,
            'workoutPlans'      => $assignWorkoutPlanCount,
            'completedSessions' => $completedSessions,
        ];

        return response()->json([
            'assignDietPlanCount'    => $assignDietPlanCount,
            'assignWorkoutPlanCount' => $assignWorkoutPlanCount,
            'completedSessions'      => $completedSessions,
            'pendingRequests'        => $pendingRequests,
            'upcomingSessions'       => $upcomingSessions,
            'dates'                  => $dates,
            'dietCounts'             => $dietCounts,
            'workoutCounts'          => $workoutCounts,
            'bookingCounts'          => $bookingCounts,
            'sessionTypeData'        => $sessionTypeData,
        ]);
    }

}
