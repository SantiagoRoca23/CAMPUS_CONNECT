@extends('layouts.app')

@section('title', 'Nueva solicitud · Campus Connect')
@section('heading', 'Crear solicitud')
@section('subtitle', 'Registra un requerimiento con evidencia opcional.')

@section('content')
    <div class="panel" style="max-width:820px;">
        <form method="POST" action="{{ route('solicitudes.store') }}" enctype="multipart/form-data" class="form-grid">
            @csrf
            <label>
                Título
                <input type="text" name="titulo" value="{{ old('titulo') }}" required>
            </label>
            <label>
                Descripción
                <textarea name="descripcion" required>{{ old('descripcion') }}</textarea>
            </label>
            <div class="form-row">
                <label>
                    Tipo
                    <select name="tipo" required>
                        @foreach($tipos as $value => $label)
                            <option value="{{ $value }}" @selected(old('tipo') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Prioridad
                    <select name="prioridad" required>
                        @foreach($prioridades as $value => $label)
                            <option value="{{ $value }}" @selected(old('prioridad', 'media') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="form-row">
                <label>
                    Ubicación
                    <input type="text" name="ubicacion" value="{{ old('ubicacion') }}" placeholder="Ej. Bloque B, aula 204">
                </label>
                <label>
                    Recurso relacionado
                    <select name="recurso_id">
                        <option value="">Ninguno</option>
                        @foreach($recursos as $recurso)
                            <option value="{{ $recurso->id }}" @selected(old('recurso_id') == $recurso->id)>
                                {{ $recurso->codigo }} · {{ $recurso->nombre }}
                            </option>
                        @endforeach
                    </select>
                </label>
            </div>
            <label>
                Evidencia (imagen o PDF)
                <input type="file" name="evidencia" accept=".jpg,.jpeg,.png,.webp,.pdf">
            </label>
            <div style="display:flex; gap:10px;">
                <button class="btn btn-primary" type="submit">Registrar solicitud</button>
                <a href="{{ route('solicitudes.index') }}" class="btn btn-light">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
