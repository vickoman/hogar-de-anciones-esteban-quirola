<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\NotaEnfermeriaController;
use App\Http\Controllers\NotaMedicaController;
use App\Http\Controllers\InformeMedicoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\RestrictUsersCrud;
use Inertia\Inertia;

Route::get('/', function () {
    if (Auth::check()) {
        // Redirige al dashboard si está autenticado
        return redirect()->route('dashboard');
    }
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::middleware(RestrictUsersCrud::class)->group(function () {
        Route::resource('/users', UserController::class);
        Route::get('api/users/search', [UserController::class, 'search'])->name('users.search');
    });

    Route::resource('/residentes', PacienteController::class);
    Route::get('api/residentes/search', [PacienteController::class, 'search'])->name('pacientes.search');
    Route::resource('/notas-enfermeria', NotaEnfermeriaController::class);
    Route::resource('/notas-medicas', NotaMedicaController::class);
    Route::resource('/informes-medicos', InformeMedicoController::class);
});

require __DIR__.'/auth.php';
