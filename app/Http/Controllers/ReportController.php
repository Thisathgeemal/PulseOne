<?php
namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

}
