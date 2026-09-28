@extends('layouts.app')

@section('title', 'Solicitudes · Campus Connect')
@section('heading', 'Solicitudes')
@section('subtitle', 'Registro, seguimiento y trazabilidad de requerimientos.')

@section('actions')
    <a href="{{ route('solicitudes.create') }}" class="btn btn-primary">Crear solicitud</a>
@endsection

@section('content')
    <form method="GET" class="filters panel" style="padding:14px;">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar...">
        <select name="estado">
            <option value="">Estado</option>
            @foreach($estados as $value => $label)
                <option value="{{ $value }}" @selected(request('estado') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="tipo">
            <option value="">Tipo</option>
            @foreach($tipos as $value => $label)
                <option value="{{ $value }}" @selected(request('tipo') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="prioridad">
            <option value="">Prioridad</option>
            @foreach($prioridades as $value => $label)
                <option value="{{ $value }}" @selected(request('prioridad') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn btn-light" type="submit">Filtrar</button>
    </form>

    <div class="panel table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Solicitante</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($solicitudes as $solicitud)
                    <tr>
                        <td>{{ $solicitud->codigo }}</td>
                        <td>{{ $solicitud->titulo }}</td>
                        <td>{{ $solicitud->tipo->label() }}</td>
                        <td><span class="badge badge-{{ $solicitud->prioridad->value }}">{{ $solicitud->prioridad->label() }}</span></td>
                        <td><span class="badge badge-{{ $solicitud->estado->value }}">{{ $solicitud->estado->label() }}</span></td>
                        <td>{{ $solicitud->solicitante?->name }}</td>
                        <td><a class="btn btn-light" href="{{ route('solicitudes.show', $solicitud) }}">Ver</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7">No hay solicitudes para mostrar.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $solicitudes->links('pagination::simple-default') }}</div>
    </div>
@endsection
