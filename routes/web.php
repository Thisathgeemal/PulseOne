<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/features', function () {
    return view('features');
})->name('features');

Route::get('/challenges', function () {
    return view('challenges');
})->name('challenges');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);

Route::get('register', [LoginController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [LoginController::class, 'register']);

Route::post('logout', [LoginController::class, 'logout']);

Route::get('selectRole', [LoginController::class, 'showRoleSelection'])->name('select.role');
Route::post('selectRole', [LoginController::class, 'selectRole']);

Route::get('admin/dashboard', fn() => 'Admin Dashboard')->name('admin.dashboard');
Route::get('trainer/dashboard', fn() => 'Trainer Dashboard')->name('trainer.dashboard');
Route::get('dietitian/dashboard', fn() => 'Dietitian Dashboard')->name('dietitian.dashboard');
Route::get('member/dashboard', fn() => 'Member Dashboard')->name('member.dashboard');
