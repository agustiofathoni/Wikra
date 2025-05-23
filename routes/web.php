<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\BoardListController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::get('/forgotpwd', function () {
        return view('auth.forgotpwd');
    })->name('forgotpwd');
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/forgot-password', [AuthController::class, 'forgotpwd']);

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Board routes
    Route::get('/dashboard', [BoardController::class, 'index'])->name('dashboard');
    Route::get('/boards/{board}', [BoardController::class, 'show'])->name('boards.show');
    Route::post('/boards', [BoardController::class, 'store'])->name('boards.store');
    Route::put('/boards/{board}', [BoardController::class, 'update'])->name('boards.update');
    Route::delete('/boards/{board}', [BoardController::class, 'destroy'])->name('boards.destroy');

    // List routes - Note: reorder route must come BEFORE the other list routes
    Route::put('/lists/reorder', [BoardListController::class, 'reorder'])->name('lists.reorder');
    Route::post('/lists/{board}', [BoardListController::class, 'store'])->name('lists.store');
    Route::put('/lists/{list}', [BoardListController::class, 'update'])->name('lists.update');
    Route::delete('/lists/{list}', [BoardListController::class, 'destroy'])->name('lists.destroy');

    // Task routes
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
});
