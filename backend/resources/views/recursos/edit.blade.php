@extends('layouts.app')

@section('title', 'Editar recurso · Campus Connect')
@section('heading', 'Editar recurso')
@section('subtitle', $recurso->codigo)

@section('content')
    <div class="panel" style="max-width:720px;">
        <form method="POST" action="{{ route('recursos.update', $recurso) }}" class="form-grid">
            @csrf
            @method('PUT')
            <div class="form-row">
                <label>Código <input type="text" name="codigo" value="{{ old('codigo', $recurso->codigo) }}" required></label>
                <label>Nombre <input type="text" name="nombre" value="{{ old('nombre', $recurso->nombre) }}" required></label>
            </div>
            <div class="form-row">
                <label>Tipo <input type="text" name="tipo" value="{{ old('tipo', $recurso->tipo) }}" required></label>
                <label>Ubicación <input type="text" name="ubicacion" value="{{ old('ubicacion', $recurso->ubicacion) }}" required></label>
            </div>
            <label>
                Estado
                <select name="estado" required>
                    @foreach($estados as $value => $label)
                        <option value="{{ $value }}" @selected(old('estado', $recurso->estado->value) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label>Descripción <textarea name="descripcion">{{ old('descripcion', $recurso->descripcion) }}</textarea></label>
            <button class="btn btn-primary" type="submit">Actualizar</button>
        </form>
    </div>
@endsection
