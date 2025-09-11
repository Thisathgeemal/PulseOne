<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\Api\UserPreferenceController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SecuritySettingsController;
use App\Http\Controllers\DietitianDashboardController;
use App\Http\Controllers\DietitianProfileController;
use App\Http\Controllers\DietPlanController;
use App\Http\Controllers\DietRequestController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\ExerciseLogController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HealthAssessmentController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\MealLogController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\MemberBookingController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\MemberProfileController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MembershipTypeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TrainerBookingController;
use App\Http\Controllers\TrainerDashboardController;
use App\Http\Controllers\TrainerProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkoutPlanController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\WorkoutRequestController;
use App\Http\Controllers\TrainerDashboardController;
use App\Http\Controllers\DietitianDashboardController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedbackController;

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
    Route::get('/member/dashboard', [MemberDashboardController::class, 'dashboard'])->name('Member.dashboard');
    Route::get('/dietitian/dashboard', [DietitianDashboardController::class, 'dashboard'])->name('Dietitian.dashboard');
    Route::get('/trainer/dashboard', [TrainerDashboardController::class, 'dashboard'])->name('Trainer.dashboard');
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'dashboard'])->name('Admin.dashboard');
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

    // Payment Management
    Route::get('/payment', [PaymentController::class, 'getPaymentData'])->name('admin.payment');

    // Report Generation Route
    Route::post('/user/report', [ReportController::class, 'generateUserReport'])->name('user.report');
    Route::post('/role/report', [ReportController::class, 'generateRoleReport'])->name('role.report');
    Route::post('/membership/report', [ReportController::class, 'generateMembershipReport'])->name('membership.report');
    Route::post('/membertype/report', [ReportController::class, 'generateMembertypeReport'])->name('membertype.report');
    Route::post('/attendance/report', [ReportController::class, 'generateAttendanceReport'])->name('attendance.report');
    Route::post('/payment/report', [ReportController::class, 'generatePaymentReport'])->name('payment.report');

    // Chat Routes
    Route::view('/message', 'adminDashboard.message')->name('admin.message');

    // Feedback Management
    Route::get('/feedback', [FeedbackController::class, 'adminIndex'])->name('admin.feedback');
    Route::patch('/feedback/{id}/toggle', [FeedbackController::class, 'adminToggleVisibility'])->name('admin.feedback.toggle');

    // Analytics Routes
    Route::get('/report', [ReportController::class, 'getReportView'])->name('admin.report');
    Route::get('/report/monthly-revenue', [ReportController::class, 'monthlyRevenue'])->name('admin.report.monthlyRevenue');
    Route::get('/report/monthly-users', [ReportController::class, 'getUserAnalytics'])->name('admin.report.monthlyUsers');
    Route::get('/report/monthly-sessions', [ReportController::class, 'getMonthlySessions'])->name('admin.report.monthlySessions');
    Route::get('/report/monthly-feedback', [ReportController::class, 'getMonthlyFeedback'])->name('admin.report.monthlyFeedback');
});

// Dietitian role routing
Route::middleware(['auth'])->prefix('dietitian')->group(function () {

    // Profile
    Route::get('/profile', [UserController::class, 'getMemberData'])->name('dietitian.profile');
    Route::put('/profile/update', [DietitianProfileController::class, 'update'])->name('dietitian.profile.update');
    Route::delete('/profile/remove-image', [DietitianProfileController::class, 'removeImage'])->name('dietitian.profile.removeImage');
    Route::post('/profile/check-password', [DietitianProfileController::class, 'checkPassword'])->name('dietitian.profile.checkPassword');

    // Diet Request Management
    Route::get('/request', [DietRequestController::class, 'index'])->name('dietitian.request');
    Route::post('/request/update-status/{id}', [DietRequestController::class, 'updateStatus'])->name('dietitian.request.update');
    Route::get('/requests/{dietRequest}', [DietRequestController::class, 'show'])->name('dietitian.requests.show');
    Route::post('/requests/{dietRequest}/assign', [DietRequestController::class, 'assign'])->name('dietitian.requests.assign');

    // Meal Library Management
    Route::get('/meals', [MealController::class, 'index'])->name('dietitian.meals');
    Route::get('/meals/create', [MealController::class, 'create'])->name('dietitian.meals.create');
    Route::post('/meals', [MealController::class, 'store'])->name('dietitian.meals.store');
    Route::get('/meals/{meal}', [MealController::class, 'show'])->name('dietitian.meals.show');
    Route::get('/meals/{meal}/edit', [MealController::class, 'edit'])->name('dietitian.meals.edit');
    Route::put('/meals/{meal}', [MealController::class, 'update'])->name('dietitian.meals.update');
    Route::delete('/meals/{meal}', [MealController::class, 'destroy'])->name('dietitian.meals.destroy');

    // Nutrition API Integration Routes
    Route::post('/meals/calculate-nutrition', [MealController::class, 'calculateNutrition'])->name('meals.calculate-nutrition');
    Route::post('/meals/for-member', [MealController::class, 'getMealsForMember'])->name('meals.for-member');
    Route::post('/meals/suggest-plan', [MealController::class, 'suggestMealPlan'])->name('meals.suggest-plan');

    // Diet Plan Management
    Route::get('/dietplan', [DietPlanController::class, 'index'])->name('dietitian.dietplan');
    Route::get('/dietplan/create/{request_id}', [DietPlanController::class, 'create'])->name('dietitian.dietplan.create');
    Route::post('/dietplan', [DietPlanController::class, 'store'])->name('dietitian.dietplan.store');
    Route::get('/dietplan/{dietPlan}', [DietPlanController::class, 'show'])->name('dietitian.dietplan.show');
    Route::get('/dietplan/{dietPlan}/track', [DietPlanController::class, 'track'])->name('dietitian.dietplan.track');
    Route::get('/dietplan/{dietPlan}/download', [ReportController::class, 'generateDietReport'])->name('dietitian.dietplan.download');
    Route::post('/dietplan/{dietPlan}/cancel', [DietPlanController::class, 'cancel'])->name('dietitian.dietplan.cancel');

    // Health Assessment Integration Routes
    Route::post('/diet-plans/meals-for-member', [DietPlanController::class, 'getMealsForMember'])->name('diet-plans.meals-for-member');
    Route::post('/diet-plans/suggest-plan', [DietPlanController::class, 'suggestMealPlan'])->name('diet-plans.suggest-plan');
    Route::post('/diet-plans/member-profile', [DietPlanController::class, 'getMemberProfile'])->name('diet-plans.member-profile');
    Route::get('/request', [DietRequestController::class, 'index'])->name('dietitian.request');
    Route::post('/request/update/{id}', [DietRequestController::class, 'updateStatus'])->name('dietitian.request.update');

    // Health Assessment Routes (for dietitians to view member assessments)
    Route::get('/member/{memberId}/health-assessment', [HealthAssessmentController::class, 'viewMemberAssessmentDietitian'])->name('dietitian.member.health-assessment');
    Route::get('/member/{memberId}/health-assessment/pdf', [ReportController::class, 'generateMemberHealthReport'])->name('dietitian.member.health-assessment.pdf');
    Route::get('/member-health-assessments', [HealthAssessmentController::class, 'dietitianHealthAssessments'])->name('dietitian.member.health-assessments');

    // Chat Routes
    Route::view('/message', 'dietitianDashboard.message')->name('dietitian.message');
    Route::get('/feedback', [FeedbackController::class, 'dietitianIndex'])->name('dietitian.feedback');
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

    // Health Assessment Routes (for trainers to view member assessments)
    Route::get('/member/{memberId}/health-assessment', [HealthAssessmentController::class, 'viewMemberAssessmentTrainer'])->name('trainer.member.health-assessment');
    Route::get('/member/{memberId}/health-assessment/pdf', [ReportController::class, 'generateMemberHealthReport'])->name('trainer.member.health-assessment.pdf');
    Route::get('/member-health-assessments', [HealthAssessmentController::class, 'trainerHealthAssessments'])->name('trainer.member.health-assessments');

    // Trainer Booking Routes
    Route::get('/bookings/requests', [TrainerBookingController::class, 'index'])->name('trainer.bookings.requests');
    Route::post('/bookings/{booking}/approve', [TrainerBookingController::class, 'approve'])->whereNumber('booking')->name('trainer.bookings.approve');
    Route::post('/bookings/{booking}/decline', [TrainerBookingController::class, 'decline'])->whereNumber('booking')->name('trainer.bookings.decline');
    Route::get('/bookings/sessions', [TrainerBookingController::class, 'sessions'])->name('trainer.bookings.sessions');
    Route::post('/bookings/{booking}/cancel', [TrainerBookingController::class, 'cancel'])
        ->whereNumber('booking')
        ->name('trainer.bookings.cancel');

    // Chat Routes
    Route::view('/message', 'trainerDashboard.message')->name('trainer.message');

    // Feedback Management (controller-powered)
    Route::get('/feedback', [FeedbackController::class, 'trainerIndex'])->name('trainer.feedback');
});

// Member role routing
Route::middleware(['auth'])->prefix('member')->group(function () {

    // Profile Routes
    Route::get('/profile', [UserController::class, 'getMemberData'])->name('member.profile');
    Route::put('/member/profile', [MemberProfileController::class, 'update'])->name('member.profile.update');
    Route::delete('/member/profile/remove-image', [MemberProfileController::class, 'removeImage'])->name('member.profile.removeImage');
    Route::post('/member/profile/check-password', [MemberProfileController::class, 'checkPassword'])->name('member.profile.checkPassword');

    // Workout Plan Routes
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
        Route::get('check', [WorkoutPlanController::class, 'checkWorkoutPlan'])->name('check');
    });

    // Diet Plan Routes
    Route::prefix('dietplan')->name('member.dietplan.')->group(function () {
        Route::get('request', [DietPlanController::class, 'request'])->name('request');
        Route::post('request', [DietPlanController::class, 'requestDietPlan'])->name('request');
        Route::get('myplan', [DietPlanController::class, 'myPlan'])->name('myplan');
        Route::get('progress/{dietPlan?}', [DietPlanController::class, 'progressTracking'])->name('progress');
        Route::get('view/{dietPlan}', [DietPlanController::class, 'viewMemberPlan'])->name('view');
        Route::get('cancel/{dietPlan}', [DietPlanController::class, 'cancelMemberPlan'])->name('cancel');
        Route::get('download/{dietPlan}', [ReportController::class, 'generateDietReport'])->name('download');
        Route::post('photo', [MealLogController::class, 'storeImage'])->name('photo');
        Route::post('mealCompletion', [MealLogController::class, 'storeMealLog'])->name('mealCompletion.store');
        Route::post('weight', [MealLogController::class, 'storeWeightLog'])->name('weight.store');
    });

    // Attendance Routes
    Route::get('/qr', [AttendanceController::class, 'showMemberScanner'])->name('member.qr');
    Route::post('/checkin', [AttendanceController::class, 'checkin'])->name('checkin');
    Route::get('/attendance', [AttendanceController::class, 'viewMemberAttendance'])->name('member.attendance');
    Route::post('/attendance/checkout/{id}', [AttendanceController::class, 'checkout'])->name('attendance.checkout');

    // Payment Management Routes
    Route::get('/payment', [PaymentController::class, 'getMemberPaymentData'])->name('member.payment');
    Route::post('/payment/report', [ReportController::class, 'generateMemberPaymentReport'])->name('member.payment.report');

    // Chat Routes
    Route::view('/message', 'memberDashboard.message')->name('member.message');

    // Membership Routes
    Route::get('/membership', [MembershipController::class, 'getLoggedInMembershipData'])->name('member.membership');
    Route::post('/membership', [MembershipController::class, 'buyMembership'])->name('member.membership.buy');
    Route::post('/membership/report', [ReportController::class, 'generateMemberMembershipReport'])->name('member.membership.report');

    // Booking Routes
    Route::get('/bookings', [MemberBookingController::class, 'index'])->name('member.bookings.index');
    Route::post('/bookings', [MemberBookingController::class, 'store'])->name('member.bookings.store');
    Route::post('/bookings/{booking}/cancel', [MemberBookingController::class, 'cancel'])
        ->whereNumber('booking')
        ->name('member.bookings.cancel');
    Route::get('/bookings/slots', [MemberBookingController::class, 'slots'])->name('member.bookings.slots');
    Route::get('/bookings/sessions', [MemberBookingController::class, 'sessions'])->name('member.bookings.sessions');

    // Health Assessment Routes
    Route::get('/health-assessment', [HealthAssessmentController::class, 'create'])->name('member.health-assessment');
    Route::post('/health-assessment', [HealthAssessmentController::class, 'store'])->name('member.health-assessment.store');
    Route::get('/health-assessment/status', [HealthAssessmentController::class, 'checkStatus'])->name('member.health-assessment.status');

     // Feedback Management (controller-powered)
    Route::get('/feedback',        [FeedbackController::class, 'memberIndex'])->name('member.feedback');
    Route::get('/feedback/create', [FeedbackController::class, 'create'])->name('member.feedback.create');
    Route::post('/feedback',       [FeedbackController::class, 'store'])->name('member.feedback.store');

    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('member.leaderboard');
    Route::get('/leaderboard/monthly', [LeaderboardController::class, 'monthly'])->name('member.leaderboard.monthly');

    // Appearance Settings
    Route::view('/appearance', 'memberDashboard.appearance')->name('member.appearance');
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

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'fetch'])->name('notifications');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
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

// chart api
Route::get('/api/weight-chart/{dietPlanId}', [DietPlanController::class, 'getWeightChartData']);

// User Preferences API Routes
Route::middleware('auth')->group(function () {
    Route::prefix('api/user/preferences')->group(function () {
        Route::get('{key}', [UserPreferenceController::class, 'show'])->name('api.preferences.show');
        Route::put('{key}', [UserPreferenceController::class, 'update'])->name('api.preferences.update');
        Route::delete('{key}/reset', [UserPreferenceController::class, 'reset'])->name('api.preferences.reset');
        Route::get('export/all', [UserPreferenceController::class, 'export'])->name('api.preferences.export');
        Route::post('import/all', [UserPreferenceController::class, 'import'])->name('api.preferences.import');
    });
});
