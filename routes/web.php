<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    // User dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Admin redirect to Filament admin panel
    Route::get('/admin-redirect', function () {
        if (!auth()->user()->hasRole('admin')) {
            return redirect('/redirect');
        }
        return redirect('/admin');
    })->name('admin.dashboard');
    
    // Technician redirect to technician panel
    Route::get('/technician', function () {
        if (!auth()->user()->hasRole('technician')) {
            return redirect('/redirect');
        }
        return redirect('/technician/dashboard');
    })->name('technician.dashboard');
    
    // Role-based redirect - sends users to their appropriate dashboard
    Route::get('/redirect', function () {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            return redirect('/admin-redirect');
        } elseif ($user->hasRole('technician')) {
            return redirect('/technician/dashboard');
        } elseif ($user->hasRole('user')) {
            return redirect('/dashboard');
        }
        
        return redirect('/')->with('error', 'No role assigned');
    })->name('role.redirect');
});
