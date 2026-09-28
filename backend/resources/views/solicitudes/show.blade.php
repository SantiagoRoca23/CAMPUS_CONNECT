@extends('layouts.app')

@section('title', $solicitud->codigo.' · Campus Connect')
@section('heading', $solicitud->titulo)
@section('subtitle', $solicitud->codigo.' · Seguimiento y comunicación')

@section('actions')
    @if(auth()->user()->isStaff())
        <a href="{{ route('solicitudes.edit', $solicitud) }}" class="btn btn-light">Editar</a>
        <form method="POST" action="{{ route('solicitudes.destroy', $solicitud) }}" onsubmit="return confirm('¿Eliminar esta solicitud?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger" type="submit">Eliminar</button>
        </form>
    @endif
@endsection

@section('content')
    <div class="grid-2">
        <div class="stack">
            <div class="panel">
                <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:14px;">
                    <span class="badge badge-{{ $solicitud->estado->value }}">{{ $solicitud->estado->label() }}</span>
                    <span class="badge badge-{{ $solicitud->prioridad->value }}">{{ $solicitud->prioridad->label() }}</span>
                    <span class="badge">{{ $solicitud->tipo->label() }}</span>
                </div>
                <p>{{ $solicitud->descripcion }}</p>
                <p><strong>Ubicación:</strong> {{ $solicitud->ubicacion ?: 'No indicada' }}</p>
                <p><strong>Solicitante:</strong> {{ $solicitud->solicitante?->name }}</p>
                <p><strong>Responsable:</strong> {{ $solicitud->responsable?->name ?: 'Sin asignar' }}</p>
                <p><strong>Recurso:</strong> {{ $solicitud->recurso?->nombre ?: 'N/A' }}</p>
            </div>

            <div class="panel">
                <h2 style="margin-top:0; font-family: var(--display);">Seguimiento</h2>
                <div class="timeline">
                    @forelse($solicitud->seguimientos as $item)
                        <div class="timeline-item">
                            <strong>{{ $item->estado_nuevo->label() }}</strong>
                            <div>{{ $item->nota }}</div>
                            <small>{{ $item->usuario?->name }} · {{ $item->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    @empty
                        <p>Sin eventos de seguimiento.</p>
                    @endforelse
                </div>
            </div>

            <div class="panel">
                <h2 style="margin-top:0; font-family: var(--display);">Comentarios</h2>
                @forelse($comentarios as $comentario)
                    <div class="comment">
                        <strong>{{ $comentario->usuario?->name }}</strong>
                        @if($comentario->es_interno)
                            <span class="badge">Interno</span>
                        @endif
                        <p style="margin:8px 0;">{{ $comentario->contenido }}</p>
                        <small>{{ $comentario->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                @empty
                    <p>No hay comentarios todavía.</p>
                @endforelse

                <form method="POST" action="{{ route('solicitudes.comentarios', $solicitud) }}" class="form-grid" style="margin-top:16px;">
                    @csrf
                    <label>
                        Nuevo comentario
                        <textarea name="contenido" required></textarea>
                    </label>
                    @if(auth()->user()->isStaff())
                        <label style="display:flex; align-items:center; gap:8px; font-weight:500;">
                            <input type="checkbox" name="es_interno" value="1" style="width:auto;">
                            Comentario interno (solo staff)
                        </label>
                    @endif
                    <button class="btn btn-primary" type="submit">Publicar</button>
                </form>
            </div>
        </div>

        <div class="stack">
            <div class="panel">
                <h2 style="margin-top:0; font-family: var(--display);">Evidencias</h2>
                @forelse($solicitud->evidencias as $evidencia)
                    <div style="display:flex; justify-content:space-between; gap:10px; margin-bottom:10px;">
                        <div>
                            <strong>{{ $evidencia->nombre_original }}</strong>
                            <div><small>{{ $evidencia->usuario?->name }}</small></div>
                        </div>
                        <a class="btn btn-light" href="{{ asset('storage/'.$evidencia->ruta) }}" target="_blank">Abrir</a>
                    </div>
                @empty
                    <p>Sin evidencias adjuntas.</p>
                @endforelse

                <form method="POST" action="{{ route('solicitudes.evidencias', $solicitud) }}" enctype="multipart/form-data" class="form-grid" style="margin-top:14px;">
                    @csrf
                    <label>
                        Adjuntar evidencia
                        <input type="file" name="evidencia" accept=".jpg,.jpeg,.png,.webp,.pdf" required>
                    </label>
                    <button class="btn btn-accent" type="submit">Subir</button>
                </form>
            </div>

            @if(auth()->user()->isStaff())
                <div class="panel">
                    <h2 style="margin-top:0; font-family: var(--display);">Gestión administrativa</h2>
                    <form method="POST" action="{{ route('solicitudes.estado', $solicitud) }}" class="form-grid">
                        @csrf
                        <label>
                            Cambiar estado
                            <select name="estado">
                                @foreach($estados as $value => $label)
                                    <option value="{{ $value }}" @selected($solicitud->estado->value === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            Nota
                            <input type="text" name="nota" placeholder="Motivo del cambio">
                        </label>
                        <button class="btn btn-primary" type="submit">Actualizar estado</button>
                    </form>

                    <form method="POST" action="{{ route('solicitudes.asignar', $solicitud) }}" class="form-grid" style="margin-top:18px;">
                        @csrf
                        <label>
                            Asignar responsable
                            <select name="asignado_a" required>
                                <option value="">Seleccione...</option>
                                @foreach($staff as $persona)
                                    <option value="{{ $persona->id }}" @selected($solicitud->asignado_a === $persona->id)>
                                        {{ $persona->name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        <button class="btn btn-accent" type="submit">Asignar</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
