<?php

namespace App\Http\Controllers\Api;

use App\Enums\EstadoSolicitud;
use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function resumen(Request $request): JsonResponse
    {
        if (! $request->user()->isStaff()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $base = Solicitud::query();

        $porTipo = (clone $base)
            ->select('tipo', DB::raw('count(*) as total'))
            ->groupBy('tipo')
            ->pluck('total', 'tipo');

        $porPrioridad = (clone $base)
            ->select('prioridad', DB::raw('count(*) as total'))
            ->groupBy('prioridad')
            ->pluck('total', 'prioridad');

        $porEstado = (clone $base)
            ->select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return response()->json([
            'data' => [
                'total' => (clone $base)->count(),
                'pendientes' => (clone $base)->where('estado', EstadoSolicitud::Pendiente)->count(),
                'cerradas' => (clone $base)->whereIn('estado', [
                    EstadoSolicitud::Cerrada,
                    EstadoSolicitud::Resuelta,
                ])->count(),
                'por_tipo' => $porTipo,
                'por_prioridad' => $porPrioridad,
                'por_estado' => $porEstado,
            ],
        ]);
    }
}
