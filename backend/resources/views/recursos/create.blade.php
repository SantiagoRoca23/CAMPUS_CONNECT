@extends('layouts.app')

@section('title', 'Nuevo recurso · Campus Connect')
@section('heading', 'Registrar recurso')
@section('subtitle', 'Alta de recurso institucional.')

@section('content')
    <div class="panel" style="max-width:720px;">
        <form method="POST" action="{{ route('recursos.store') }}" class="form-grid">
            @csrf
            <div class="form-row">
                <label>Código <input type="text" name="codigo" value="{{ old('codigo') }}" required></label>
                <label>Nombre <input type="text" name="nombre" value="{{ old('nombre') }}" required></label>
            </div>
            <div class="form-row">
                <label>Tipo <input type="text" name="tipo" value="{{ old('tipo') }}" required placeholder="Proyector, aula, red..."></label>
                <label>Ubicación <input type="text" name="ubicacion" value="{{ old('ubicacion') }}" required></label>
            </div>
            <label>
                Estado
                <select name="estado" required>
                    @foreach($estados as $value => $label)
                        <option value="{{ $value }}" @selected(old('estado', 'disponible') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label>Descripción <textarea name="descripcion">{{ old('descripcion') }}</textarea></label>
            <button class="btn btn-primary" type="submit">Guardar</button>
        </form>
    </div>
@endsection
