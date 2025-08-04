<?php
namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkoutPlan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    // User detail report
    public function generateUserReport(Request $request)
    {
        $datetimeInput = $request->input('datetime');
        $role          = $request->input('role');

        if (! $datetimeInput || ! $role) {
            abort(400, 'Missing required parameters.');
        }

        $datetime      = Carbon::parse($datetimeInput);
        $formattedDate = $datetime->format('Y-m-d');

        $users = User::whereHas('roles', fn($q) => $q->where('role_name', $role))->get();

        $pdf = Pdf::loadView('report.userReport', [
            'formattedDate' => $formattedDate,
            'role'          => $role,
            'users'         => $users,
        ]);

        return $pdf->download("{$role}_Report.pdf");
    }

    // Role detail report
    public function generateRoleReport(Request $request)
    {
        $datetimeInput = $request->input('datetime');

        if (! $datetimeInput) {
            abort(400, 'Missing required parameters.');
        }

        $datetime      = Carbon::parse($datetimeInput);
        $formattedDate = $datetime->format('Y-m-d');

        $roles = Role::withCount('users')->get();

        $pdf = Pdf::loadView('report.roleReport', [
            'formattedDate' => $formattedDate,
            'roles'         => $roles,
        ]);

        return $pdf->download("Role_Report.pdf");
    }

    // Membership detail report
    public function generateMembershipReport(Request $request)
    {
        $datetimeInput = $request->input('datetime');

        if (! $datetimeInput) {
            abort(400, 'Missing required parameters.');
        }

        $datetime       = Carbon::parse($datetimeInput);
        $formattedDate  = $datetime->format('Y-m-d');
        $memberships    = Membership::all();
        $membershipType = MembershipType::all();

        $pdf = Pdf::loadView('report.membershipReport', [
            'formattedDate'  => $formattedDate,
            'memberships'    => $memberships,
            'membershipType' => $membershipType,
        ]);

        return $pdf->download("Membership_Report.pdf");
    }

    // Membertype detail report
    public function generateMembertypeReport(Request $request)
    {
        $datetimeInput = $request->input('datetime');

        if (! $datetimeInput) {
            abort(400, 'Missing required parameters.');
        }

        $datetime      = Carbon::parse($datetimeInput);
        $formattedDate = $datetime->format('Y-m-d');

        $memberTypes = MembershipType::all();

        $pdf = Pdf::loadView('report.membertypeReport', [
            'formattedDate' => $formattedDate,
            'memberTypes'   => $memberTypes,
        ]);

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
        $date             = Carbon::now()->format('Y-m-d');

        $pdf = Pdf::loadView('report.workoutplanReport', [
            'plan'             => $plan,
            'groupedExercises' => $groupedExercises,
            'date'             => $date,
        ]);

        return $pdf->download("WorkoutPlan_Report_{$plan->plan_name}.pdf");
    }

    // Attendance report
    public function generateAttendanceReport()
    {
        $user = Auth::user();
        $date = now()->format('Y-m-d');

        // Get all attendance records with related user and role info
        $attendances = Attendance::with('user.roles')
            ->orderBy('check_in_time', 'desc')
            ->get();

        // Load the PDF view and pass data
        $pdf = Pdf::loadView('report.attendanceReport', [
            'attendances' => $attendances,
            'date'        => $date,
        ]);

        return $pdf->download("Attendance_Records_{$date}.pdf");
    }

}
