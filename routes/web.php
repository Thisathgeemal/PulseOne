<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('home'))->name('home');
Route::get('/about', fn() => view('about'))->name('about');
Route::get('/features', fn() => view('features'))->name('features');
Route::get('/challenges', fn() => view('challenges'))->name('challenges');
Route::get('/contact', fn() => view('contact'))->name('contact');

Route::get('/login', fn() => view('auth.login'))->name('login');
Route::get('/2fa', fn() => view('auth.2fa'))->name('2fa');
Route::get('/selectRole', fn() => view('auth.selectRole'))->name('selectRole');

Route::post('login', [LoginController::class, 'login']);
Route::post('2fa', [LoginController::class, 'verify2FA'])->name('2fa.verify');
Route::post('2fa/resend', [LoginController::class, 'resend2FA'])->name('2fa.resend');
Route::post('selectRole', [LoginController::class, 'submitSelectedRole'])->name('selectRole.submit');
Route::post('logout', [LoginController::class, 'logout']);

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register/member', [RegisterController::class, 'registerMember'])->name('register.member');
Route::post('register/payment', [RegisterController::class, 'registerPayment'])->name('register.payment');

Route::get('/member/dashboard', fn() => view('memberDashboard.dashboard'))->name('Member.dashboard');
Route::get('/dietitian/dashboard', fn() => view('dietitianDashboard.dashboard'))->name('Dietitian.dashboard');
Route::get('/trainer/dashboard', fn() => view('trainerDashboard.dashboard'))->name('Trainer.dashboard');
Route::get('/admin/dashboard', fn() => view('adminDashboard.dashboard'))->name('Admin.dashboard');
