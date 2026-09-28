<?php

namespace App\Http\Controllers\Web;

use App\Enums\EstadoSolicitud;
use App\Enums\PrioridadSolicitud;
use App\Enums\TipoSolicitud;
use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeStaff();

        $query = Solicitud::query()->with(['solicitante', 'responsable']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->string('estado'));
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->string('tipo'));
        }
        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->string('prioridad'));
        }
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->date('desde'));
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->date('hasta'));
        }

        $avgSql = DB::getDriverName() === 'pgsql'
            ? 'AVG(EXTRACT(EPOCH FROM (cerrada_at - created_at))/3600) as avg_hours'
            : 'AVG((julianday(cerrada_at) - julianday(created_at)) * 24) as avg_hours';

        $resumen = [
            'total' => (clone $query)->count(),
            'pendientes' => (clone $query)->where('estado', EstadoSolicitud::Pendiente)->count(),
            'cerradas' => (clone $query)->whereIn('estado', [
                EstadoSolicitud::Cerrada,
                EstadoSolicitud::Resuelta,
            ])->count(),
            'tiempo_promedio_horas' => round((clone $query)
                ->whereNotNull('cerrada_at')
                ->selectRaw($avgSql)
                ->value('avg_hours') ?? 0, 2),
        ];

        $porTipo = (clone $query)
            ->select('tipo', DB::raw('count(*) as total'))
            ->groupBy('tipo')
            ->pluck('total', 'tipo');

        $porPrioridad = (clone $query)
            ->select('prioridad', DB::raw('count(*) as total'))
            ->groupBy('prioridad')
            ->pluck('total', 'prioridad');

        $porResponsable = (clone $query)
            ->whereNotNull('asignado_a')
            ->join('users', 'users.id', '=', 'solicitudes.asignado_a')
            ->select('users.name', DB::raw('count(*) as total'))
            ->groupBy('users.name')
            ->pluck('total', 'name');

        return view('reportes.index', [
            'resumen' => $resumen,
            'porTipo' => $porTipo,
            'porPrioridad' => $porPrioridad,
            'porResponsable' => $porResponsable,
            'solicitudes' => $query->latest()->paginate(15)->withQueryString(),
            'estados' => EstadoSolicitud::options(),
            'tipos' => TipoSolicitud::options(),
            'prioridades' => PrioridadSolicitud::options(),
        ]);
    }

    public function exportar(Request $request): StreamedResponse
    {
        $this->authorizeStaff();

        $query = Solicitud::with(['solicitante', 'responsable'])->latest();

        if ($request->filled('estado')) {
            $query->where('estado', $request->string('estado'));
        }

        $filename = 'reporte_solicitudes_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Codigo', 'Titulo', 'Tipo', 'Prioridad', 'Estado',
                'Solicitante', 'Responsable', 'Creada', 'Cerrada',
            ]);

            $query->chunk(100, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->codigo,
                        $row->titulo,
                        $row->tipo->value,
                        $row->prioridad->value,
                        $row->estado->value,
                        $row->solicitante?->name,
                        $row->responsable?->name,
                        $row->created_at?->toDateTimeString(),
                        $row->cerrada_at?->toDateTimeString(),
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function authorizeStaff(): void
    {
        if (! auth()->user()?->isStaff()) {
            abort(403);
        }
    }
}
