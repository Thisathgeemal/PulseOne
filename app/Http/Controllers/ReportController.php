<?php
namespace App\Http\Controllers;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // trainer detail report
    public function generateTrainerReport(Request $request)
    {
        $datetimeInput = $request->input('datetime');

        $datetime      = Carbon::parse($datetimeInput);
        $formattedDate = $datetime->format('Y-m-d');

        $trainers = User::whereHas('roles', fn($q) => $q->where('role_name', 'Trainer'))->get();

        $pdf = Pdf::loadView('report.trainerReport', compact('formattedDate', 'trainers'));
        return $pdf->download('Trainer_Report.pdf');
    }

    // admin detail report
    public function generateAdminReport(Request $request)
    {
        $datetimeInput = $request->input('datetime');

        $datetime      = Carbon::parse($datetimeInput);
        $formattedDate = $datetime->format('Y-m-d');

        $admins = User::whereHas('roles', fn($q) => $q->where('role_name', 'Admin'))->get();

        $pdf = Pdf::loadView('report.adminReport', compact('formattedDate', 'admins'));
        return $pdf->download('Admin_Report.pdf');
    }

    // dietitian detail report
    public function generateDietitianReport(Request $request)
    {
        $datetimeInput = $request->input('datetime');

        $datetime      = Carbon::parse($datetimeInput);
        $formattedDate = $datetime->format('Y-m-d');

        $dietitians = User::whereHas('roles', fn($q) => $q->where('role_name', 'Dietitian'))->get();

        $pdf = Pdf::loadView('report.dietitianReport', compact('formattedDate', 'dietitians'));
        return $pdf->download('Dietitian_Report.pdf');
    }
}
