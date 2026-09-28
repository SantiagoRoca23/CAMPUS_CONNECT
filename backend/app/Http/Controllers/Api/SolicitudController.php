<?php

namespace App\Http\Controllers\Api;

use App\Enums\EstadoSolicitud;
use App\Enums\PrioridadSolicitud;
use App\Enums\TipoSolicitud;
use App\Http\Controllers\Controller;
use App\Models\Comentario;
use App\Models\Evidencia;
use App\Models\Seguimiento;
use App\Models\Solicitud;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Solicitud::with(['solicitante', 'responsable', 'recurso', 'evidencias'])
            ->latest();

        if (! $user->isStaff()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->string('estado'));
        }

        return response()->json([
            'data' => $query->paginate((int) $request->integer('per_page', 15)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:160'],
            'descripcion' => ['required', 'string', 'min:10'],
            'tipo' => ['required', 'in:'.implode(',', array_column(TipoSolicitud::cases(), 'value'))],
            'prioridad' => ['nullable', 'in:'.implode(',', array_column(PrioridadSolicitud::cases(), 'value'))],
            'ubicacion' => ['nullable', 'string', 'max:160'],
            'recurso_id' => ['nullable', 'exists:recursos,id'],
            'evidencia' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,webp'],
        ]);

        $solicitud = Solicitud::create([
            'codigo' => Solicitud::generarCodigo(),
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'],
            'tipo' => $data['tipo'],
            'prioridad' => $data['prioridad'] ?? PrioridadSolicitud::Media->value,
            'estado' => EstadoSolicitud::Pendiente,
            'ubicacion' => $data['ubicacion'] ?? null,
            'recurso_id' => $data['recurso_id'] ?? null,
            'user_id' => $request->user()->id,
        ]);

        Seguimiento::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => $request->user()->id,
            'estado_anterior' => null,
            'estado_nuevo' => EstadoSolicitud::Pendiente,
            'nota' => 'Solicitud registrada desde API.',
        ]);

        if ($request->hasFile('evidencia')) {
            $this->persistEvidencia($request, $solicitud);
        }

        $solicitud->load(['solicitante', 'evidencias', 'seguimientos']);

        return response()->json([
            'message' => 'Solicitud creada.',
            'data' => $solicitud,
        ], 201);
    }

    public function show(Request $request, Solicitud $solicitud): JsonResponse
    {
        $this->authorizeView($request, $solicitud);

        $solicitud->load([
            'solicitante',
            'responsable',
            'recurso',
            'evidencias.usuario',
            'seguimientos.usuario',
        ]);

        $comentarios = $solicitud->comentarios()
            ->with('usuario')
            ->when(! $request->user()->isStaff(), fn ($q) => $q->where('es_interno', false))
            ->latest()
            ->get();

        return response()->json([
            'data' => $solicitud,
            'comentarios' => $comentarios,
        ]);
    }

    public function update(Request $request, Solicitud $solicitud): JsonResponse
    {
        if (! $request->user()->isStaff() && (int) $solicitud->user_id !== (int) $request->user()->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $data = $request->validate([
            'titulo' => ['sometimes', 'string', 'max:160'],
            'descripcion' => ['sometimes', 'string', 'min:10'],
            'tipo' => ['sometimes', 'in:'.implode(',', array_column(TipoSolicitud::cases(), 'value'))],
            'prioridad' => ['sometimes', 'in:'.implode(',', array_column(PrioridadSolicitud::cases(), 'value'))],
            'ubicacion' => ['nullable', 'string', 'max:160'],
            'estado' => ['sometimes', 'in:'.implode(',', array_column(EstadoSolicitud::cases(), 'value'))],
            'asignado_a' => ['nullable', 'exists:users,id'],
            'nota' => ['nullable', 'string', 'max:500'],
        ]);

        if (isset($data['estado']) && ! $request->user()->isStaff()) {
            return response()->json(['message' => 'Solo personal administrativo puede cambiar el estado.'], 403);
        }

        if (array_key_exists('asignado_a', $data) && ! $request->user()->isStaff()) {
            return response()->json(['message' => 'Solo personal administrativo puede asignar responsables.'], 403);
        }

        $estadoAnterior = $solicitud->estado;

        $solicitud->fill(collect($data)->except(['nota', 'estado'])->all());

        if (isset($data['estado'])) {
            $estadoNuevo = EstadoSolicitud::from($data['estado']);
            $solicitud->estado = $estadoNuevo;
            $solicitud->cerrada_at = in_array($estadoNuevo, [
                EstadoSolicitud::Cerrada,
                EstadoSolicitud::Resuelta,
                EstadoSolicitud::Cancelada,
            ], true) ? now() : null;
        }

        $solicitud->save();

        if (isset($estadoNuevo) && $estadoAnterior !== $estadoNuevo) {
            Seguimiento::create([
                'solicitud_id' => $solicitud->id,
                'user_id' => $request->user()->id,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $estadoNuevo,
                'nota' => $data['nota'] ?? 'Cambio de estado vía API.',
            ]);
        }

        return response()->json([
            'message' => 'Solicitud actualizada.',
            'data' => $solicitud->fresh(['solicitante', 'responsable', 'seguimientos']),
        ]);
    }

    public function destroy(Request $request, Solicitud $solicitud): JsonResponse
    {
        if (! $request->user()->isStaff()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $solicitud->delete();

        return response()->json(['message' => 'Solicitud eliminada.']);
    }

    public function seguimientos(Request $request, Solicitud $solicitud): JsonResponse
    {
        $this->authorizeView($request, $solicitud);

        return response()->json([
            'data' => $solicitud->seguimientos()->with('usuario')->latest()->get(),
        ]);
    }

    public function comentarios(Request $request, Solicitud $solicitud): JsonResponse
    {
        $this->authorizeView($request, $solicitud);

        $comentarios = $solicitud->comentarios()
            ->with('usuario')
            ->when(! $request->user()->isStaff(), fn ($q) => $q->where('es_interno', false))
            ->latest()
            ->get();

        return response()->json(['data' => $comentarios]);
    }

    public function storeComentario(Request $request, Solicitud $solicitud): JsonResponse
    {
        $this->authorizeView($request, $solicitud);

        $data = $request->validate([
            'contenido' => ['required', 'string', 'min:2', 'max:2000'],
            'es_interno' => ['sometimes', 'boolean'],
        ]);

        $comentario = Comentario::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => $request->user()->id,
            'contenido' => $data['contenido'],
            'es_interno' => $request->user()->isStaff() && $request->boolean('es_interno'),
        ]);

        return response()->json([
            'message' => 'Comentario creado.',
            'data' => $comentario->load('usuario'),
        ], 201);
    }

    public function storeEvidencia(Request $request, Solicitud $solicitud): JsonResponse
    {
        $this->authorizeView($request, $solicitud);

        $request->validate([
            'evidencia' => ['required', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,webp'],
        ]);

        $evidencia = $this->persistEvidencia($request, $solicitud);

        return response()->json([
            'message' => 'Evidencia adjuntada.',
            'data' => $evidencia,
        ], 201);
    }

    private function persistEvidencia(Request $request, Solicitud $solicitud): Evidencia
    {
        $file = $request->file('evidencia');
        $path = $file->store('evidencias/'.$solicitud->id, 'public');

        return Evidencia::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => $request->user()->id,
            'nombre_original' => $file->getClientOriginalName(),
            'ruta' => $path,
            'mime_type' => $file->getClientMimeType(),
            'tamanio' => $file->getSize(),
        ]);
    }

    private function authorizeView(Request $request, Solicitud $solicitud): void
    {
        $user = $request->user();

        if ($user->isStaff() || (int) $solicitud->user_id === (int) $user->id) {
            return;
        }

        abort(403, 'No autorizado.');
    }
}
