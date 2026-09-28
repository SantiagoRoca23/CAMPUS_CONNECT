@extends('layouts.app')

@section('title', 'Reportes · Campus Connect')
@section('heading', 'Reportes consolidados')
@section('subtitle', 'Indicadores de atención, tipología y responsables.')

@section('actions')
    <a href="{{ route('reportes.exportar', request()->query()) }}" class="btn btn-accent">Exportar CSV</a>
@endsection

@section('content')
    <form method="GET" class="filters panel" style="padding:14px;">
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
        <input type="date" name="desde" value="{{ request('desde') }}">
        <input type="date" name="hasta" value="{{ request('hasta') }}">
        <button class="btn btn-light" type="submit">Aplicar</button>
    </form>

    <div class="grid-stats">
        <div class="stat-card"><span>Total filtrado</span><strong>{{ $resumen['total'] }}</strong></div>
        <div class="stat-card"><span>Pendientes</span><strong>{{ $resumen['pendientes'] }}</strong></div>
        <div class="stat-card"><span>Cerradas</span><strong>{{ $resumen['cerradas'] }}</strong></div>
        <div class="stat-card"><span>Tiempo promedio (h)</span><strong>{{ $resumen['tiempo_promedio_horas'] }}</strong></div>
    </div>

    <div class="grid-2" style="margin-bottom:16px;">
        <div class="panel">
            <h2 style="margin-top:0; font-family: var(--display);">Por tipo</h2>
            @forelse($porTipo as $tipo => $total)
                <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                    <span>{{ $tipos[$tipo] ?? $tipo }}</span>
                    <strong>{{ $total }}</strong>
                </div>
            @empty
                <p>Sin datos.</p>
            @endforelse
        </div>
        <div class="panel">
            <h2 style="margin-top:0; font-family: var(--display);">Por responsable</h2>
            @forelse($porResponsable as $nombre => $total)
                <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                    <span>{{ $nombre }}</span>
                    <strong>{{ $total }}</strong>
                </div>
            @empty
                <p>Sin asignaciones.</p>
            @endforelse
        </div>
    </div>

    <div class="panel table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Título</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th>Responsable</th>
                    <th>Creada</th>
                </tr>
            </thead>
            <tbody>
                @foreach($solicitudes as $solicitud)
                    <tr>
                        <td>{{ $solicitud->codigo }}</td>
                        <td>{{ $solicitud->titulo }}</td>
                        <td>{{ $solicitud->estado->label() }}</td>
                        <td>{{ $solicitud->prioridad->label() }}</td>
                        <td>{{ $solicitud->responsable?->name ?: '—' }}</td>
                        <td>{{ $solicitud->created_at->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagination">{{ $solicitudes->links('pagination::simple-default') }}</div>
    </div>
@endsection
