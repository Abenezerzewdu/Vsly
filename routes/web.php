<?php

use App\Http\Controllers\DuelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TakeController;
use App\Http\Controllers\VoteController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

   
//takes route
Route::get('/takes', [TakeController::class, 'index'])->name('takes.index');
Route::get('/takes/{take}', [TakeController::class, 'show'])->name('takes.show');

Route::middleware('auth')->group(function () {
    Route::get('/takes/create', [TakeController::class, 'create'])->name('takes.create');
    Route::post('/takes', [TakeController::class, 'store'])->name('takes.store');
    Route::get('/takes/{take}/edit', [TakeController::class, 'edit'])->name('takes.edit');
    Route::patch('/takes/{take}', [TakeController::class, 'update'])->name('takes.update');
    Route::delete('/takes/{take}', [TakeController::class, 'destroy'])->name('takes.destroy');
});


Route::post('/takes/{take}/challenge', [DuelController::class, 'store'])
    ->name('duels.store')
    ->middleware('auth');

//duel
Route::get('/duels/{duel}', [DuelController::class, 'show'])
    ->name('duels.show');

Route::post('/duels/{duel}/move', [DuelController::class, 'submitMove'])
    ->name('duels.move')
    ->middleware('auth');

//vote
Route::post('/duels/{duel}/vote', [VoteController::class, 'store'])
    ->name('duels.vote')
    ->middleware('auth');

require __DIR__.'/auth.php';
