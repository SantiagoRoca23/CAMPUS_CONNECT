<?php

namespace App\Http\Controllers\Web;

use App\Enums\EstadoSolicitud;
use App\Enums\PrioridadSolicitud;
use App\Enums\TipoSolicitud;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Comentario;
use App\Models\Evidencia;
use App\Models\Recurso;
use App\Models\Seguimiento;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SolicitudController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Solicitud::with(['solicitante', 'responsable', 'recurso'])->latest();

        if (! $user->isStaff()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->string('estado'));
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->string('tipo'));
        }

        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->string('prioridad'));
        }

        if ($request->filled('q')) {
            $term = '%'.$request->string('q').'%';
            $query->where(function ($q) use ($term) {
                $q->where('titulo', 'like', $term)
                    ->orWhere('codigo', 'like', $term)
                    ->orWhere('descripcion', 'like', $term);
            });
        }

        return view('solicitudes.index', [
            'solicitudes' => $query->paginate(10)->withQueryString(),
            'estados' => EstadoSolicitud::options(),
            'tipos' => TipoSolicitud::options(),
            'prioridades' => PrioridadSolicitud::options(),
        ]);
    }

    public function create(): View
    {
        return view('solicitudes.create', [
            'tipos' => TipoSolicitud::options(),
            'prioridades' => PrioridadSolicitud::options(),
            'recursos' => Recurso::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:160'],
            'descripcion' => ['required', 'string', 'min:10'],
            'tipo' => ['required', 'in:'.implode(',', array_column(TipoSolicitud::cases(), 'value'))],
            'prioridad' => ['required', 'in:'.implode(',', array_column(PrioridadSolicitud::cases(), 'value'))],
            'ubicacion' => ['nullable', 'string', 'max:160'],
            'recurso_id' => ['nullable', 'exists:recursos,id'],
            'evidencia' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,webp'],
        ], [
            'titulo.required' => 'El título es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres.',
            'evidencia.mimes' => 'La evidencia debe ser imagen o PDF.',
        ]);

        $solicitud = Solicitud::create([
            'codigo' => Solicitud::generarCodigo(),
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'],
            'tipo' => $data['tipo'],
            'prioridad' => $data['prioridad'],
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
            'nota' => 'Solicitud registrada.',
        ]);

        if ($request->hasFile('evidencia')) {
            $this->guardarEvidencia($request, $solicitud);
        }

        return redirect()
            ->route('solicitudes.show', $solicitud)
            ->with('success', 'Solicitud creada correctamente.');
    }

    public function show(Solicitud $solicitud): View
    {
        $this->authorizeView($solicitud);

        $solicitud->load([
            'solicitante',
            'responsable',
            'recurso',
            'evidencias.usuario',
            'seguimientos.usuario',
        ]);

        $comentarios = $solicitud->comentarios()
            ->with('usuario')
            ->when(! auth()->user()->isStaff(), fn ($q) => $q->where('es_interno', false))
            ->latest()
            ->get();

        return view('solicitudes.show', [
            'solicitud' => $solicitud,
            'comentarios' => $comentarios,
            'estados' => EstadoSolicitud::options(),
            'staff' => User::whereIn('role', [
                UserRole::Administrativo->value,
                UserRole::Administrador->value,
            ])->orderBy('name')->get(),
        ]);
    }

    public function edit(Solicitud $solicitud): View
    {
        $this->authorizeStaff();

        return view('solicitudes.edit', [
            'solicitud' => $solicitud,
            'tipos' => TipoSolicitud::options(),
            'prioridades' => PrioridadSolicitud::options(),
            'estados' => EstadoSolicitud::options(),
            'recursos' => Recurso::orderBy('nombre')->get(),
            'staff' => User::whereIn('role', [
                UserRole::Administrativo->value,
                UserRole::Administrador->value,
            ])->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Solicitud $solicitud): RedirectResponse
    {
        $this->authorizeStaff();

        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:160'],
            'descripcion' => ['required', 'string', 'min:10'],
            'tipo' => ['required', 'in:'.implode(',', array_column(TipoSolicitud::cases(), 'value'))],
            'prioridad' => ['required', 'in:'.implode(',', array_column(PrioridadSolicitud::cases(), 'value'))],
            'ubicacion' => ['nullable', 'string', 'max:160'],
            'recurso_id' => ['nullable', 'exists:recursos,id'],
            'asignado_a' => ['nullable', 'exists:users,id'],
            'estado' => ['required', 'in:'.implode(',', array_column(EstadoSolicitud::cases(), 'value'))],
            'nota' => ['nullable', 'string', 'max:500'],
        ]);

        $estadoAnterior = $solicitud->estado;
        $estadoNuevo = EstadoSolicitud::from($data['estado']);

        $solicitud->update([
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'],
            'tipo' => $data['tipo'],
            'prioridad' => $data['prioridad'],
            'ubicacion' => $data['ubicacion'] ?? null,
            'recurso_id' => $data['recurso_id'] ?? null,
            'asignado_a' => $data['asignado_a'] ?? null,
            'estado' => $estadoNuevo,
            'cerrada_at' => in_array($estadoNuevo, [EstadoSolicitud::Cerrada, EstadoSolicitud::Resuelta, EstadoSolicitud::Cancelada], true)
                ? now()
                : null,
        ]);

        if ($estadoAnterior !== $estadoNuevo) {
            Seguimiento::create([
                'solicitud_id' => $solicitud->id,
                'user_id' => $request->user()->id,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $estadoNuevo,
                'nota' => $data['nota'] ?? 'Cambio de estado.',
            ]);
        }

        return redirect()
            ->route('solicitudes.show', $solicitud)
            ->with('success', 'Solicitud actualizada.');
    }

    public function destroy(Solicitud $solicitud): RedirectResponse
    {
        $this->authorizeStaff();
        $solicitud->delete();

        return redirect()
            ->route('solicitudes.index')
            ->with('success', 'Solicitud eliminada.');
    }

    public function cambiarEstado(Request $request, Solicitud $solicitud): RedirectResponse
    {
        $this->authorizeStaff();

        $data = $request->validate([
            'estado' => ['required', 'in:'.implode(',', array_column(EstadoSolicitud::cases(), 'value'))],
            'nota' => ['nullable', 'string', 'max:500'],
        ]);

        $estadoAnterior = $solicitud->estado;
        $estadoNuevo = EstadoSolicitud::from($data['estado']);

        $solicitud->update([
            'estado' => $estadoNuevo,
            'cerrada_at' => in_array($estadoNuevo, [EstadoSolicitud::Cerrada, EstadoSolicitud::Resuelta, EstadoSolicitud::Cancelada], true)
                ? now()
                : null,
        ]);

        Seguimiento::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => $request->user()->id,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $estadoNuevo,
            'nota' => $data['nota'] ?? 'Actualización de estado.',
        ]);

        return back()->with('success', 'Estado actualizado.');
    }

    public function asignar(Request $request, Solicitud $solicitud): RedirectResponse
    {
        $this->authorizeStaff();

        $data = $request->validate([
            'asignado_a' => ['required', 'exists:users,id'],
        ]);

        $solicitud->update(['asignado_a' => $data['asignado_a']]);

        if ($solicitud->estado === EstadoSolicitud::Pendiente) {
            $solicitud->update(['estado' => EstadoSolicitud::EnProceso]);
            Seguimiento::create([
                'solicitud_id' => $solicitud->id,
                'user_id' => $request->user()->id,
                'estado_anterior' => EstadoSolicitud::Pendiente,
                'estado_nuevo' => EstadoSolicitud::EnProceso,
                'nota' => 'Responsable asignado.',
            ]);
        }

        return back()->with('success', 'Responsable asignado.');
    }

    public function adjuntarEvidencia(Request $request, Solicitud $solicitud): RedirectResponse
    {
        $this->authorizeView($solicitud);

        $request->validate([
            'evidencia' => ['required', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,webp'],
        ]);

        $this->guardarEvidencia($request, $solicitud);

        return back()->with('success', 'Evidencia adjuntada.');
    }

    public function comentar(Request $request, Solicitud $solicitud): RedirectResponse
    {
        $this->authorizeView($solicitud);

        $data = $request->validate([
            'contenido' => ['required', 'string', 'min:2', 'max:2000'],
            'es_interno' => ['sometimes', 'boolean'],
        ]);

        Comentario::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => $request->user()->id,
            'contenido' => $data['contenido'],
            'es_interno' => $request->user()->isStaff() && $request->boolean('es_interno'),
        ]);

        return back()->with('success', 'Comentario registrado.');
    }

    private function guardarEvidencia(Request $request, Solicitud $solicitud): Evidencia
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

    private function authorizeView(Solicitud $solicitud): void
    {
        $user = auth()->user();

        if ($user->isStaff() || (int) $solicitud->user_id === (int) $user->id) {
            return;
        }

        abort(403);
    }

    private function authorizeStaff(): void
    {
        if (! auth()->user()->isStaff()) {
            abort(403);
        }
    }
}
