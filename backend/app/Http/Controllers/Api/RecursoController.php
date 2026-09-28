<?php

namespace App\Http\Controllers\Api;

use App\Enums\EstadoRecurso;
use App\Http\Controllers\Controller;
use App\Models\Recurso;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecursoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Recurso::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $request->user()->isStaff()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:40', 'unique:recursos,codigo'],
            'nombre' => ['required', 'string', 'max:160'],
            'tipo' => ['required', 'string', 'max:80'],
            'ubicacion' => ['required', 'string', 'max:160'],
            'estado' => ['required', 'in:'.implode(',', array_column(EstadoRecurso::cases(), 'value'))],
            'descripcion' => ['nullable', 'string'],
        ]);

        $recurso = Recurso::create($data);

        return response()->json([
            'message' => 'Recurso creado.',
            'data' => $recurso,
        ], 201);
    }

    public function show(Recurso $recurso): JsonResponse
    {
        return response()->json(['data' => $recurso]);
    }

    public function update(Request $request, Recurso $recurso): JsonResponse
    {
        if (! $request->user()->isStaff()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $data = $request->validate([
            'codigo' => ['sometimes', 'string', 'max:40', 'unique:recursos,codigo,'.$recurso->id],
            'nombre' => ['sometimes', 'string', 'max:160'],
            'tipo' => ['sometimes', 'string', 'max:80'],
            'ubicacion' => ['sometimes', 'string', 'max:160'],
            'estado' => ['sometimes', 'in:'.implode(',', array_column(EstadoRecurso::cases(), 'value'))],
            'descripcion' => ['nullable', 'string'],
        ]);

        $recurso->update($data);

        return response()->json([
            'message' => 'Recurso actualizado.',
            'data' => $recurso,
        ]);
    }

    public function destroy(Request $request, Recurso $recurso): JsonResponse
    {
        if (! $request->user()->isStaff()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $recurso->delete();

        return response()->json(['message' => 'Recurso eliminado.']);
    }
}
