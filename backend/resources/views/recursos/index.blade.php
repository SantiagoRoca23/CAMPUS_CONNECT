@extends('layouts.app')

@section('title', 'Recursos · Campus Connect')
@section('heading', 'Gestión de recursos')
@section('subtitle', 'Inventario institucional asociado a solicitudes.')

@section('actions')
    <a href="{{ route('recursos.create') }}" class="btn btn-primary">Nuevo recurso</a>
@endsection

@section('content')
    <div class="panel table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Ubicación</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recursos as $recurso)
                    <tr>
                        <td>{{ $recurso->codigo }}</td>
                        <td>{{ $recurso->nombre }}</td>
                        <td>{{ $recurso->tipo }}</td>
                        <td>{{ $recurso->ubicacion }}</td>
                        <td><span class="badge">{{ $recurso->estado->label() }}</span></td>
                        <td style="display:flex; gap:8px;">
                            <a href="{{ route('recursos.edit', $recurso) }}" class="btn btn-light">Editar</a>
                            <form method="POST" action="{{ route('recursos.destroy', $recurso) }}" onsubmit="return confirm('¿Eliminar recurso?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No hay recursos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $recursos->links('pagination::simple-default') }}</div>
    </div>
@endsection
