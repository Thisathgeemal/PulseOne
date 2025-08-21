<?php
namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\Payment;
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
}
