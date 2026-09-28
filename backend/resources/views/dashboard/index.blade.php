@extends('layouts.app')

@section('title', 'Dashboard · Campus Connect')
@section('heading', 'Dashboard administrativo')
@section('subtitle', 'Resumen consolidado de solicitudes y su ciclo de vida.')

@section('actions')
    <a href="{{ route('solicitudes.create') }}" class="btn btn-accent">Nueva solicitud</a>
@endsection

@section('content')
    <div class="grid-stats">
        <div class="stat-card">
            <span>Total</span>
            <strong>{{ $totales['total'] }}</strong>
        </div>
        <div class="stat-card">
            <span>Pendientes</span>
            <strong>{{ $totales['pendientes'] }}</strong>
        </div>
        <div class="stat-card">
            <span>En proceso</span>
            <strong>{{ $totales['en_proceso'] }}</strong>
        </div>
        <div class="stat-card">
            <span>Cerradas / resueltas</span>
            <strong>{{ $totales['cerradas'] }}</strong>
        </div>
    </div>

    <div class="grid-2">
        <div class="panel">
            <h2 style="margin-top:0; font-family: var(--display);">Solicitudes recientes</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Título</th>
                            <th>Estado</th>
                            <th>Prioridad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recientes as $item)
                            <tr>
                                <td><a href="{{ route('solicitudes.show', $item) }}">{{ $item->codigo }}</a></td>
                                <td>{{ $item->titulo }}</td>
                                <td><span class="badge badge-{{ $item->estado->value }}">{{ $item->estado->label() }}</span></td>
                                <td><span class="badge badge-{{ $item->prioridad->value }}">{{ $item->prioridad->label() }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4">Aún no hay solicitudes registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="stack">
            <div class="panel">
                <h2 style="margin-top:0; font-family: var(--display);">Por tipo</h2>
                @foreach($tipos as $key => $label)
                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                        <span>{{ $label }}</span>
                        <strong>{{ $porTipo[$key] ?? 0 }}</strong>
                    </div>
                @endforeach
            </div>
            <div class="panel">
                <h2 style="margin-top:0; font-family: var(--display);">Por prioridad</h2>
                @foreach($prioridades as $key => $label)
                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                        <span>{{ $label }}</span>
                        <strong>{{ $porPrioridad[$key] ?? 0 }}</strong>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
