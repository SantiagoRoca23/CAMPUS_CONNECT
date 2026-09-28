@extends('layouts.app')

@section('title', 'Editar solicitud · Campus Connect')
@section('heading', 'Editar solicitud')
@section('subtitle', $solicitud->codigo)

@section('content')
    <div class="panel" style="max-width:820px;">
        <form method="POST" action="{{ route('solicitudes.update', $solicitud) }}" class="form-grid">
            @csrf
            @method('PUT')
            <label>
                Título
                <input type="text" name="titulo" value="{{ old('titulo', $solicitud->titulo) }}" required>
            </label>
            <label>
                Descripción
                <textarea name="descripcion" required>{{ old('descripcion', $solicitud->descripcion) }}</textarea>
            </label>
            <div class="form-row">
                <label>
                    Tipo
                    <select name="tipo" required>
                        @foreach($tipos as $value => $label)
                            <option value="{{ $value }}" @selected(old('tipo', $solicitud->tipo->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Prioridad
                    <select name="prioridad" required>
                        @foreach($prioridades as $value => $label)
                            <option value="{{ $value }}" @selected(old('prioridad', $solicitud->prioridad->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="form-row">
                <label>
                    Estado
                    <select name="estado" required>
                        @foreach($estados as $value => $label)
                            <option value="{{ $value }}" @selected(old('estado', $solicitud->estado->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Responsable
                    <select name="asignado_a">
                        <option value="">Sin asignar</option>
                        @foreach($staff as $persona)
                            <option value="{{ $persona->id }}" @selected(old('asignado_a', $solicitud->asignado_a) == $persona->id)>
                                {{ $persona->name }}
                            </option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="form-row">
                <label>
                    Ubicación
                    <input type="text" name="ubicacion" value="{{ old('ubicacion', $solicitud->ubicacion) }}">
                </label>
                <label>
                    Recurso
                    <select name="recurso_id">
                        <option value="">Ninguno</option>
                        @foreach($recursos as $recurso)
                            <option value="{{ $recurso->id }}" @selected(old('recurso_id', $solicitud->recurso_id) == $recurso->id)>
                                {{ $recurso->codigo }} · {{ $recurso->nombre }}
                            </option>
                        @endforeach
                    </select>
                </label>
            </div>
            <label>
                Nota de seguimiento
                <input type="text" name="nota" value="{{ old('nota') }}" placeholder="Opcional si cambia el estado">
            </label>
            <div style="display:flex; gap:10px;">
                <button class="btn btn-primary" type="submit">Guardar cambios</button>
                <a href="{{ route('solicitudes.show', $solicitud) }}" class="btn btn-light">Volver</a>
            </div>
        </form>
    </div>
@endsection
