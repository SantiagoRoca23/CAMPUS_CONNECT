<?php

use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\RecursoController as ApiRecursoController;
use App\Http\Controllers\Api\ReporteController as ApiReporteController;
use App\Http\Controllers\Api\SolicitudController as ApiSolicitudController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [ApiAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [ApiAuthController::class, 'me']);
        Route::post('/logout', [ApiAuthController::class, 'logout']);

        Route::get('/solicitudes', [ApiSolicitudController::class, 'index']);
        Route::post('/solicitudes', [ApiSolicitudController::class, 'store']);
        Route::get('/solicitudes/{solicitud}', [ApiSolicitudController::class, 'show']);
        Route::put('/solicitudes/{solicitud}', [ApiSolicitudController::class, 'update']);
        Route::patch('/solicitudes/{solicitud}', [ApiSolicitudController::class, 'update']);
        Route::delete('/solicitudes/{solicitud}', [ApiSolicitudController::class, 'destroy']);

        Route::get('/solicitudes/{solicitud}/seguimientos', [ApiSolicitudController::class, 'seguimientos']);
        Route::get('/solicitudes/{solicitud}/comentarios', [ApiSolicitudController::class, 'comentarios']);
        Route::post('/solicitudes/{solicitud}/comentarios', [ApiSolicitudController::class, 'storeComentario']);
        Route::post('/solicitudes/{solicitud}/evidencias', [ApiSolicitudController::class, 'storeEvidencia']);

        Route::apiResource('recursos', ApiRecursoController::class)
            ->parameters(['recursos' => 'recurso'])
            ->names([
                'index' => 'api.recursos.index',
                'store' => 'api.recursos.store',
                'show' => 'api.recursos.show',
                'update' => 'api.recursos.update',
                'destroy' => 'api.recursos.destroy',
            ]);

        Route::get('/reportes/resumen', [ApiReporteController::class, 'resumen']);
    });
});
