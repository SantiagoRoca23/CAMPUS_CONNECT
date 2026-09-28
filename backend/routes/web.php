<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\RecursoController;
use App\Http\Controllers\Web\ReporteController;
use App\Http\Controllers\Web\SolicitudController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('solicitudes', SolicitudController::class)->parameters([
        'solicitudes' => 'solicitud',
    ]);
    Route::post('/solicitudes/{solicitud}/estado', [SolicitudController::class, 'cambiarEstado'])->name('solicitudes.estado');
    Route::post('/solicitudes/{solicitud}/asignar', [SolicitudController::class, 'asignar'])->name('solicitudes.asignar');
    Route::post('/solicitudes/{solicitud}/evidencias', [SolicitudController::class, 'adjuntarEvidencia'])->name('solicitudes.evidencias');
    Route::post('/solicitudes/{solicitud}/comentarios', [SolicitudController::class, 'comentar'])->name('solicitudes.comentarios');

    Route::middleware('role:administrativo,administrador')->group(function () {
        Route::resource('recursos', RecursoController::class)->except(['show'])->parameters([
            'recursos' => 'recurso',
        ]);
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/exportar', [ReporteController::class, 'exportar'])->name('reportes.exportar');
    });
});
