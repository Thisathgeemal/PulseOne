<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MemberSettingsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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

// 2FA
Route::view('/2fa', 'auth.2fa')->name('2fa');
Route::post('/2fa', [LoginController::class, 'verify2FA'])->name('2fa.verify');
Route::post('/2fa/resend', [LoginController::class, 'resend2FA'])->name('2fa.resend');

// Forgot/Reset Password
Route::get('/forgotPassword', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgotPassword', [LoginController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/resetPassword/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('/resetPassword', [LoginController::class, 'reset'])->name('password.update');

// Role selection after login
Route::view('/selectRole', 'auth.selectRole')->name('selectRole');
Route::post('/selectRole', [LoginController::class, 'submitSelectedRole'])->name('selectRole.submit');

// Registration
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register/member', [RegisterController::class, 'registerMember'])->name('register.member');
Route::post('/register/payment', [RegisterController::class, 'registerPayment'])->name('register.payment');

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

    // User roll Route
    Route::get('/role', [RoleController::class, 'getRoleData'])->name('admin.role');
    Route::post('/role', [RoleController::class, 'createRole'])->name('role.create');
    Route::post('/role/delete', [RoleController::class, 'deleteRole'])->name('role.delete');

    // Profile
    Route::get('/profile', [UserController::class, 'getMemberData'])->name('admin.profile');

    // // Settings
    // Route::get('/settings', [UserController::class, 'showSettings'])->name('admin.settings');

    // Static View Routes
    Route::view('/attendance', 'adminDashboard.attendance')->name('admin.attendance');
    Route::view('/message', 'adminDashboard.message')->name('admin.message');
    Route::view('/payment', 'adminDashboard.payment')->name('admin.payment');
    Route::view('/feedback', 'adminDashboard.feedback')->name('admin.feedback');
    Route::view('/report', 'adminDashboard.report')->name('admin.report');
    Route::view('/membership', 'adminDashboard.membership')->name('admin.membership');

});

// Dietitian role routing
Route::middleware(['auth'])->prefix('dietitian')->group(function () {

    // Profile
    Route::get('/profile', [UserController::class, 'getMemberData'])->name('dietitian.profile');

    // // Settings
    // Route::get('/settings', [UserController::class, 'showSettings'])->name('admin.settings');

    // Static View Routes
    Route::view('/request', 'dietitianDashboard.request')->name('dietitian.request');
    Route::view('/dietplan', 'dietitianDashboard.dietplan')->name('dietitian.dietplan');
    Route::view('/meals', 'dietitianDashboard.meals')->name('dietitian.meals');
    Route::view('/feedback', 'dietitianDashboard.feedback')->name('dietitian.feedback');
    Route::view('/message', 'dietitianDashboard.message')->name('dietitian.message');

});

// Trainer role routing
Route::middleware(['auth'])->prefix('trainer')->group(function () {

    // Profile
    Route::get('/profile', [UserController::class, 'getTrainerData'])->name('trainer.profile');

    // // Settings
    // Route::get('/settings', [UserController::class, 'showSettings'])->name('trainer.settings');

    // Static View Routes
    Route::view('/request', 'trainerDashboard.request')->name('trainer.request');
    Route::view('/workoutplan', 'trainerDashboard.workoutplan')->name('trainer.workoutplan');
    Route::view('/exercises', 'trainerDashboard.exercises')->name('trainer.exercises');
    Route::view('/booking', 'trainerDashboard.booking')->name('trainer.booking');
    Route::view('/message', 'trainerDashboard.message')->name('trainer.message');
    Route::view('/feedback', 'trainerDashboard.feedback')->name('trainer.feedback');

});

// Member role routing
Route::middleware(['auth'])->group(function () {
    // 👤 Member views QR scanner and past attendance
    Route::get('/member/qrscanner', [AttendanceController::class, 'showMemberQR'])->name('member.qrscanner');

    // 🔘 Manual attendance marking from inside dashboard (button)
    Route::post('/member/mark-attendance', [AttendanceController::class, 'markAttendance'])->name('mark.attendance');

    // 📱 QR scan route - accessed via GET (no form, from QR code scan)
    Route::get('/mark-attendance', [AttendanceController::class, 'markAttendanceViaQR'])->name('mark.attendance.qr');

    Route::view('/member/workoutplan', 'memberDashboard.workoutplan')->name('Member.workoutplan');
    Route::view('/member/dietplan', 'memberDashboard.dietplan')->name('Member.dietplan');
    Route::view('/member/bookings', 'memberDashboard.bookings')->name('Member.bookings');
    Route::view('/member/message', 'memberDashboard.message')->name('Member.message');
    Route::view('/member/leaderboard', 'memberDashboard.leaderboard')->name('Member.leaderboard');
    Route::view('/member/payment', 'memberDashboard.payment')->name('Member.payment');

    /*
    |--------------------------------------------------------------------------
    | Member Settings
    |--------------------------------------------------------------------------
    */
    Route::get('/member/settings', [MemberSettingsController::class, 'index'])->name('Member.settings');
    Route::put('/member/settings', [MemberSettingsController::class, 'update'])->name('Member.settings.update');
    Route::delete('/member/settings/remove-image', [MemberSettingsController::class, 'removeImage'])->name('Member.settings.removeImage');
    Route::post('/member/settings/check-password', [MemberSettingsController::class, 'checkPassword'])->name('Member.settings.checkPassword');
});
