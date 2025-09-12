<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Feedback;
use App\Models\Payment;
use App\Models\Membership;
use App\Models\HealthAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function dashboard()
    {
        $admin = Auth::user();
        
        // Admin-specific statistics
        $stats = [
            'total_members' => User::whereHas('roles', function($q) {
                $q->where('role_name', 'Member');
            })->count(),
            
            'total_trainers' => User::whereHas('roles', function($q) {
                $q->where('role_name', 'Trainer');
            })->count(),
            
            'total_dietitians' => User::whereHas('roles', function($q) {
                $q->where('role_name', 'Dietitian');
            })->count(),
            
            'total_payments' => Payment::whereDate('payment_date', Carbon::today())->sum('amount'),
        ];
        
        // Recent registrations (last 5 members)
        $recent_registrations = User::whereHas('roles', function($q) {
            $q->where('role_name', 'Member');
        })
        ->with('roles')
        ->orderByDesc('created_at')
        ->limit(4)
        ->get();
        
        // Recent feedback for admin monitoring
        $recent_feedback = Feedback::with(['fromUser', 'toUser'])
            ->where('is_visible', true)
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();
        
        // Today's payments
        $todays_payments = Payment::with(['user', 'membershipType'])
            ->whereDate('payment_date', Carbon::today())
            ->orderByDesc('payment_date')
            ->limit(4)
            ->get();
        
        // System activity (recent important actions)
        $recent_activities = collect();
        
        // Add recent member registrations to activity
        $recent_registrations->each(function($member) use ($recent_activities) {
            $recent_activities->push([
                'icon' => 'fa-user-plus',
                'message' => "New member registered: {$member->first_name} {$member->last_name}",
                'time' => $member->created_at->diffForHumans(),
                'created_at' => $member->created_at
            ]);
        });
        
        // Add recent payments to activity
        $todays_payments->each(function($payment) use ($recent_activities) {
            $recent_activities->push([
                'icon' => 'fa-money-bill-wave',
                'message' => "Payment received from {$payment->user->first_name} {$payment->user->last_name} - Rs. " . number_format($payment->amount, 2),
                'time' => Carbon::parse($payment->payment_date)->diffForHumans(),
                'created_at' => Carbon::parse($payment->payment_date)
            ]);
        });
        
        // Add recent feedback to activity
        $recent_feedback->each(function($feedback) use ($recent_activities) {
            $toType = $feedback->toUser ? "{$feedback->toUser->first_name} {$feedback->toUser->last_name}" : "System";
            $recent_activities->push([
                'icon' => 'fa-star',
                'message' => "New {$feedback->type} feedback from {$feedback->fromUser->first_name} {$feedback->fromUser->last_name} to {$toType}",
                'time' => $feedback->created_at->diffForHumans(),
                'created_at' => $feedback->created_at
            ]);
        });
        
        // Sort activities by time and limit
        $recent_activities = $recent_activities->sortByDesc('created_at')->take(8)->values();
        
        return view('adminDashboard.dashboard', compact(
            'stats',
            'recent_registrations', 
            'recent_feedback',
            'todays_payments',
            'recent_activities'
        ));
    }
}
