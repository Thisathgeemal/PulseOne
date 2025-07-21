<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('home'))->name('home');
Route::get('/about', fn() => view('about'))->name('about');
Route::get('/features', fn() => view('features'))->name('features');
Route::get('/challenges', fn() => view('challenges'))->name('challenges');
Route::get('/contact', fn() => view('contact'))->name('contact');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);

Route::get('/2fa', fn() => view('auth.2fa'))->name('2fa');
Route::post('2fa', [LoginController::class, 'verify2FA'])->name('2fa.verify');
Route::post('2fa/resend', [LoginController::class, 'resend2FA'])->name('2fa.resend');

Route::get('/forgotPassword', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('forgotPassword', [LoginController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/resetPassword/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('resetPassword', [LoginController::class, 'reset'])->name('password.update');

Route::get('/selectRole', fn() => view('auth.selectRole'))->name('selectRole');
Route::post('selectRole', [LoginController::class, 'submitSelectedRole'])->name('selectRole.submit');

Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register/member', [RegisterController::class, 'registerMember'])->name('register.member');
Route::post('register/payment', [RegisterController::class, 'registerPayment'])->name('register.payment');

Route::get('/member/dashboard', fn() => view('memberDashboard.dashboard'))->name('Member.dashboard');
Route::get('/dietitian/dashboard', fn() => view('dietitianDashboard.dashboard'))->name('Dietitian.dashboard');
Route::get('/trainer/dashboard', fn() => view('trainerDashboard.dashboard'))->name('Trainer.dashboard');
Route::get('/admin/dashboard', fn() => view('adminDashboard.dashboard'))->name('Admin.dashboard');

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

Route::middleware(['auth'])->group(function () {

    Route::get('/admin/admin', [UserController::class, 'getAdminData'])->name('admin.admin');
    Route::post('admin/admin', [UserController::class, 'createAdmin'])->name('admin.create');
    Route::post('admin/admin/update', [UserController::class, 'updateAdmin'])->name('admin.update');
    Route::post('admin/admin/bulkAction', [UserController::class, 'handleAdminAction'])->name('admin.bulkAction');
    Route::post('admin/admin/report', [ReportController::class, 'generateAdminReport'])->name('admin.report');

    Route::get('/admin/trainer', [UserController::class, 'getTrainerData'])->name('admin.trainer');
    Route::post('admin/trainer', [UserController::class, 'createTrainer'])->name('trainer.create');
    Route::post('admin/trainer/update', [UserController::class, 'updateTrainer'])->name('trainer.update');
    Route::post('admin/trainer/bulkAction', [UserController::class, 'handleTrainerAction'])->name('trainer.bulkAction');
    Route::post('admin/trainer/report', [ReportController::class, 'generateTrainerReport'])->name('trainer.report');

    Route::view('/admin/dietitian', 'adminDashboard.dietitian')->name('admin.dietitian');
    Route::view('/admin/member', 'adminDashboard.member')->name('admin.member');

    Route::view('/admin/attendance', 'adminDashboard.attendance')->name('admin.attendance');
    Route::view('/admin/message', 'adminDashboard.message')->name('admin.message');
    Route::view('/admin/payment', 'adminDashboard.payment')->name('admin.payment');
    Route::view('/admin/feedback', 'adminDashboard.feedback')->name('admin.feedback');
});
