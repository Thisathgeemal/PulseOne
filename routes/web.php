<?php

use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SecuritySettingsController;
use App\Http\Controllers\DietitianProfileController;
use App\Http\Controllers\DietPlanController;
use App\Http\Controllers\DietRequestController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\ExerciseLogController;
use App\Http\Controllers\MemberProfileController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MembershipTypeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TrainerProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkoutPlanController;
use App\Http\Controllers\WorkoutRequestController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

// Public Pages
Route::view('/', 'home')->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/features', 'features')->name('features');
Route::view('/challenges', 'challenges')->name('challenges');
Route::view('/contact', 'contact')->name('contact');

// Authentication
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Role selection after login
Route::view('/selectRole', 'auth.selectRole')->name('selectRole');
Route::post('/selectRole', [LoginController::class, 'submitSelectedRole'])->name('selectRole.submit');

// Registration
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register/member', [RegisterController::class, 'registerMember'])->name('register.member');
Route::post('/register/payment', [RegisterController::class, 'registerPayment'])->name('register.payment');

// Forgot and Reset Password
Route::get('/forgotPassword', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgotPassword', [LoginController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/resetPassword/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('/resetPassword', [LoginController::class, 'reset'])->name('password.update');

// Dashboards
Route::middleware(['auth'])->group(function () {
    Route::view('/member/dashboard', 'memberDashboard.dashboard')->name('Member.dashboard');
    Route::view('/dietitian/dashboard', 'dietitianDashboard.dashboard')->name('Dietitian.dashboard');
    Route::view('/trainer/dashboard', 'trainerDashboard.dashboard')->name('Trainer.dashboard');
    Route::view('/admin/dashboard', 'adminDashboard.dashboard')->name('Admin.dashboard');
});

// Admin role routing
Route::middleware(['auth'])->prefix('admin')->group(function () {

    // Admin Routes
    Route::get('/admin', [UserController::class, 'getAdminData'])->name('admin.admin');
    Route::post('/admin', [UserController::class, 'createAdmin'])->name('admin.create');
    Route::post('/admin/update', [UserController::class, 'updateAdmin'])->name('admin.update');
    Route::post('/admin/bulkAction', [UserController::class, 'handleAdminAction'])->name('admin.bulkAction');

    // Trainer Routes
    Route::get('/trainer', [UserController::class, 'getTrainerData'])->name('admin.trainer');
    Route::post('/trainer', [UserController::class, 'createTrainer'])->name('trainer.create');
    Route::post('/trainer/update', [UserController::class, 'updateTrainer'])->name('trainer.update');
    Route::post('/trainer/bulkAction', [UserController::class, 'handleTrainerAction'])->name('trainer.bulkAction');

    // Dietitian Routes
    Route::get('/dietitian', [UserController::class, 'getDietitianData'])->name('admin.dietitian');
    Route::post('/dietitian', [UserController::class, 'createDietitian'])->name('dietitian.create');
    Route::post('/dietitian/update', [UserController::class, 'updateDietitian'])->name('dietitian.update');
    Route::post('/dietitian/bulkAction', [UserController::class, 'handleDietitianAction'])->name('dietitian.bulkAction');

    // Member Routes
    Route::get('/member', [UserController::class, 'getMemberData'])->name('admin.member');
    Route::post('/member', [UserController::class, 'createMember'])->name('member.create');
    Route::post('/member/update', [UserController::class, 'updateMember'])->name('member.update');
    Route::post('/member/bulkAction', [UserController::class, 'handleMemberAction'])->name('member.bulkAction');

    // Report Generation Route
    Route::post('/user/report', [ReportController::class, 'generateUserReport'])->name('user.report');
    Route::post('/role/report', [ReportController::class, 'generateRoleReport'])->name('role.report');
    Route::post('/membership/report', [ReportController::class, 'generateMembershipReport'])->name('membership.report');
    Route::post('/membertype/report', [ReportController::class, 'generateMembertypeReport'])->name('membertype.report');
    Route::post('/attendance/report', [ReportController::class, 'generateAttendanceReport'])->name('attendance.report');

    // User roll Route
    Route::get('/role', [RoleController::class, 'getRoleData'])->name('admin.role');
    Route::post('/role', [RoleController::class, 'createRole'])->name('role.create');
    Route::post('/role/delete', [RoleController::class, 'deleteRole'])->name('role.delete');

    // Membership Route
    Route::get('/membership', [MembershipController::class, 'getmembershipData'])->name('admin.membership');
    Route::post('/membership', [MembershipController::class, 'createMembership'])->name('membership.create');
    Route::post('/membership/cancel', [MembershipController::class, 'cancelMembership'])->name('membership.cancel');

    // Membershiptype Route
    Route::get('/membertype', [MembershipTypeController::class, 'getMembertypeData'])->name('admin.membertype');
    Route::post('/membertype', [MembershipTypeController::class, 'createMembertype'])->name('membertype.create');
    Route::post('/membertype/update', [MembershipTypeController::class, 'updateMembertype'])->name('membertype.update');
    Route::post('/membertype/delete', [MembershipTypeController::class, 'deleteMembertype'])->name('membertype.delete');

    // Profile
    Route::get('/profile', [UserController::class, 'getAdminData'])->name('admin.profile');
    Route::put('/profile/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    Route::delete('/profile/remove-image', [AdminProfileController::class, 'removeImage'])->name('admin.profile.removeImage');
    Route::post('/profile/check-password', [AdminProfileController::class, 'checkPassword'])->name('admin.profile.checkPassword');

    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'viewAll'])->name('admin.attendance');
    Route::post('/attendance/manual', [AttendanceController::class, 'storeManual'])->name('admin.attendance.manual');
    Route::get('/admin/qr-display', [AttendanceController::class, 'showQR'])->name('admin.qr.display');
    Route::get('/admin/search-users', [AttendanceController::class, 'searchUsers'])->name('admin.search.users');

    // Static View Routes
    Route::view('/message', 'adminDashboard.message')->name('admin.message');
    Route::view('/payment', 'adminDashboard.payment')->name('admin.payment');
    Route::view('/feedback', 'adminDashboard.feedback')->name('admin.feedback');
    Route::view('/report', 'adminDashboard.report')->name('admin.report');
});

// Dietitian role routing
Route::middleware(['auth'])->prefix('dietitian')->group(function () {

    // Profile
    Route::get('/profile', [UserController::class, 'getMemberData'])->name('dietitian.profile');
    Route::put('/profile/update', [DietitianProfileController::class, 'update'])->name('dietitian.profile.update');
    Route::delete('/profile/remove-image', [DietitianProfileController::class, 'removeImage'])->name('dietitian.profile.removeImage');
    Route::post('/profile/check-password', [DietitianProfileController::class, 'checkPassword'])->name('dietitian.profile.checkPassword');

    // // Diet Plan Management
    // Route::get('/dietplan', [DietPlamController::class, 'index'])->name('dietitian.workoutplan');
    Route::get('/dietplan/create/{request_id}', [DietPlamController::class, 'create'])->name('dietitian.workoutplan.create');
    // Route::post('/dietplan/store', [DietPlamController::class, 'store'])->name('dietitian.workoutplan.store');

    // // Diet Plan Download
    // Route::get('/dietplan/view/{id}', [DietPlamController::class, 'viewPlan'])->name('dietitian.workoutplan.view');
    // Route::get('/dietplan/progress/{id}', [DietPlamController::class, 'viewProgress'])->name('dietitian.workoutplan.progress');
    // Route::get('/dietplan/download/{id}', [ReportController::class, 'generateWorkoutReport'])->name('diet.report');

    // Diet Request Management
    Route::get('/request', [DietRequestController::class, 'index'])->name('dietitian.request');
    Route::post('/request/update-status/{id}', [DietRequestController::class, 'updateStatus'])->name('dietitian.request.update');

    // Static View Routes
    Route::view('/dietplan', 'dietitianDashboard.dietplan')->name('dietitian.dietplan');
    Route::view('/meals', 'dietitianDashboard.meals')->name('dietitian.meals');
    Route::view('/feedback', 'dietitianDashboard.feedback')->name('dietitian.feedback');
    Route::view('/message', 'dietitianDashboard.message')->name('dietitian.message');

});

// Trainer role routing
Route::middleware(['auth'])->prefix('trainer')->group(function () {

    // Profile
    Route::get('trainer/profile', [UserController::class, 'getTrainerData'])->name('trainer.profile');
    Route::put('trainer/profile/update', [TrainerProfileController::class, 'update'])->name('trainer.profile.update');
    Route::delete('trainer/profile/remove-image', [TrainerProfileController::class, 'removeImage'])->name('trainer.profile.removeImage');
    Route::post('trainer/profile/check-password', [TrainerProfileController::class, 'checkPassword'])->name('trainer.profile.checkPassword');

    // Workout Plan Management
    Route::get('/workoutplan', [WorkoutPlanController::class, 'index'])->name('trainer.workoutplan');
    Route::get('/workoutplan/create/{request_id}', [WorkoutPlanController::class, 'create'])->name('trainer.workoutplan.create');
    Route::post('/workoutplan/store', [WorkoutPlanController::class, 'store'])->name('trainer.workoutplan.store');

    // Workout Plan Download
    Route::get('/workoutplan/view/{id}', [WorkoutPlanController::class, 'viewPlan'])->name('trainer.workoutplan.view');
    Route::get('/workoutplan/progress/{id}', [WorkoutPlanController::class, 'viewProgress'])->name('trainer.workoutplan.progress');
    Route::get('/workoutplan/download/{id}', [ReportController::class, 'generateWorkoutReport'])->name('workout.report');

    // Workout Request Management
    Route::get('/request', [WorkoutRequestController::class, 'index'])->name('trainer.request');
    Route::post('/request/update-status/{id}', [WorkoutRequestController::class, 'updateStatus'])->name('trainer.request.update');

    // Exercises
    Route::get('/exercises', [ExerciseController::class, 'index'])->name('trainer.exercises');
    Route::post('/exercises', [ExerciseController::class, 'store'])->name('trainer.exercises.store');
    Route::delete('/exercises/{id}', [ExerciseController::class, 'destroy'])->name('trainer.exercises.destroy');

    // Trainer Attendance Routes
    Route::get('/qr', [AttendanceController::class, 'showTrainerScanner'])->name('trainer.qr');
    Route::post('/checkin', [AttendanceController::class, 'checkin'])->name('trainer.checkin');
    Route::get('/attendance', [AttendanceController::class, 'viewTrainerAttendance'])->name('trainer.attendance');

    // Static View Routes
    Route::view('/booking', 'trainerDashboard.booking')->name('trainer.booking');
    Route::view('/message', 'trainerDashboard.message')->name('trainer.message');
    Route::view('/feedback', 'trainerDashboard.feedback')->name('trainer.feedback');

});

// Member role routing
Route::middleware(['auth'])->prefix('member')->group(function () {

    // Profile
    Route::get('/profile', [UserController::class, 'getMemberData'])->name('member.profile');
    Route::put('/member/profile', [MemberProfileController::class, 'update'])->name('member.profile.update');
    Route::delete('/member/profile/remove-image', [MemberProfileController::class, 'removeImage'])->name('member.profile.removeImage');
    Route::post('/member/profile/check-password', [MemberProfileController::class, 'checkPassword'])->name('member.profile.checkPassword');

    // Workout Plan routes
    Route::prefix('workoutplan')->name('member.workoutplan.')->group(function () {
        Route::get('request', [WorkoutPlanController::class, 'request'])->name('request');
        Route::post('request', [WorkoutPlanController::class, 'requestWorkout'])->name('request');
        Route::get('myplan', [WorkoutPlanController::class, 'myPlan'])->name('myplan');
        Route::get('progress', [WorkoutPlanController::class, 'progressTracking'])->name('progress');
        Route::post('log-exercise', [ExerciseLogController::class, 'store'])->name('exercise.log');
        Route::post('log-photo', [ExerciseLogController::class, 'storeImage'])->name('photo');
        Route::get('view/{id}', [WorkoutPlanController::class, 'viewMemberPlan'])->name('view');
        Route::get('cancel/{id}', [WorkoutPlanController::class, 'cancelMemberPlan'])->name('cancel');
        Route::get('download/{id}', [ReportController::class, 'generateWorkoutReport'])->name('download');
    });

    // Diet Plan routes
    Route::prefix('dietplan')->name('member.dietplan.')->group(function () {
        Route::get('request', [DietPlanController::class, 'request'])->name('request');
        Route::post('request', [DietPlanController::class, 'requestDietPlan'])->name('request');
        Route::get('myplan', [DietPlanController::class, 'myPlan'])->name('myplan');
        Route::get('progress', [DietPlanController::class, 'progressTracking'])->name('progress');
    });

    Route::get('/qr', [AttendanceController::class, 'showMemberScanner'])->name('member.qr');
    Route::post('/checkin', [AttendanceController::class, 'checkin'])->name('checkin');
    Route::get('/attendance', [AttendanceController::class, 'viewMemberAttendance'])->name('member.attendance');
    Route::post('/attendance/checkout/{id}', [AttendanceController::class, 'checkout'])->name('attendance.checkout');

    // Static View Routes
    Route::view('/membership', 'memberDashboard.membership')->name('member.membership');
    Route::view('/booking', 'memberDashboard.booking')->name('member.booking');
    Route::view('/payment', 'memberDashboard.payment')->name('member.payment');
    Route::view('/feedback', 'memberDashboard.feedback')->name('member.feedback');
    Route::view('/message', 'memberDashboard.message')->name('member.message');
    Route::view('/report', 'memberDashboard.report')->name('member.report');
    Route::view('/leaderboard', 'memberDashboard.leaderboard')->name('member.leaderboard');

});

// Security route
Route::middleware(['auth'])->group(function () {

    // Track and Logout User Sessions
    Route::post('/logout/device', [SecuritySettingsController::class, 'logoutDevice'])->name('security.logout.device');
    Route::post('/logout/all/devices', [SecuritySettingsController::class, 'logoutAllDevices'])->name('security.logout.all');

    // 2FA
    Route::view('/2fa', 'auth.2fa')->name('2fa');
    Route::post('/2fa', [LoginController::class, 'verify2FA'])->name('2fa.verify');
    Route::post('/2fa/resend', [LoginController::class, 'resend2FA'])->name('2fa.resend');
    Route::post('/mfa-toggle', [SecuritySettingsController::class, 'toggleMfa'])->name('settings.mfa-toggle');
});

// Redirect handler (if you're using it elsewhere)
Route::get('/checkin', [AttendanceController::class, 'handleTokenRedirect'])->name('checkin');

// Updated QR token redirect with role-based logic
Route::get('/checkin-token', function (Request $request) {
    $token = $request->query('token');
    session(['checkin_token' => $token]);

    // If user is not logged in, redirect to login
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    // Get user role
    $user = auth()->user();
    $role = $user->userRole->role->role_name ?? null;

    // Redirect to the correct QR scanner based on role
    if ($role === 'Trainer') {
        return redirect()->route('trainer.qr');
    } elseif ($role === 'Member') {
        return redirect()->route('member.qr');
    } else {
        return redirect()->route('login')->with('error', 'Unauthorized role.');
    }
})->name('checkin-token.redirect');

// Check-in submission (shared for member/trainer)
Route::post('/checkin-token', [AttendanceController::class, 'checkin'])->name('checkin-token');
