<?php

namespace App\Http\Controllers\Web;

use App\Enums\EstadoSolicitud;
use App\Enums\PrioridadSolicitud;
use App\Enums\TipoSolicitud;
use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $baseQuery = $user->isStaff()
            ? Solicitud::query()
            : Solicitud::query()->where('user_id', $user->id);

        $totales = [
            'total' => (clone $baseQuery)->count(),
            'pendientes' => (clone $baseQuery)->where('estado', EstadoSolicitud::Pendiente)->count(),
            'en_proceso' => (clone $baseQuery)->where('estado', EstadoSolicitud::EnProceso)->count(),
            'cerradas' => (clone $baseQuery)->whereIn('estado', [
                EstadoSolicitud::Cerrada,
                EstadoSolicitud::Resuelta,
            ])->count(),
        ];

        $porTipo = (clone $baseQuery)
            ->select('tipo', DB::raw('count(*) as total'))
            ->groupBy('tipo')
            ->pluck('total', 'tipo');

        $porPrioridad = (clone $baseQuery)
            ->select('prioridad', DB::raw('count(*) as total'))
            ->groupBy('prioridad')
            ->pluck('total', 'prioridad');

        $recientes = (clone $baseQuery)
            ->with(['solicitante', 'responsable'])
            ->latest()
            ->limit(8)
            ->get();

        return view('dashboard.index', [
            'totales' => $totales,
            'porTipo' => $porTipo,
            'porPrioridad' => $porPrioridad,
            'recientes' => $recientes,
            'tipos' => TipoSolicitud::options(),
            'prioridades' => PrioridadSolicitud::options(),
        ]);
    }
}
