<?php

namespace App\Http\Controllers\Web;

use App\Enums\EstadoRecurso;
use App\Http\Controllers\Controller;
use App\Models\Recurso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecursoController extends Controller
{
    public function index(): View
    {
        $this->authorizeStaff();

        return view('recursos.index', [
            'recursos' => Recurso::latest()->paginate(12),
            'estados' => EstadoRecurso::options(),
        ]);
    }

    public function create(): View
    {
        $this->authorizeStaff();

        return view('recursos.create', [
            'estados' => EstadoRecurso::options(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeStaff();

        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:40', 'unique:recursos,codigo'],
            'nombre' => ['required', 'string', 'max:160'],
            'tipo' => ['required', 'string', 'max:80'],
            'ubicacion' => ['required', 'string', 'max:160'],
            'estado' => ['required', 'in:'.implode(',', array_column(EstadoRecurso::cases(), 'value'))],
            'descripcion' => ['nullable', 'string'],
        ]);

        Recurso::create($data);

        return redirect()->route('recursos.index')->with('success', 'Recurso creado.');
    }

    public function edit(Recurso $recurso): View
    {
        $this->authorizeStaff();

        return view('recursos.edit', [
            'recurso' => $recurso,
            'estados' => EstadoRecurso::options(),
        ]);
    }

    public function update(Request $request, Recurso $recurso): RedirectResponse
    {
        $this->authorizeStaff();

        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:40', 'unique:recursos,codigo,'.$recurso->id],
            'nombre' => ['required', 'string', 'max:160'],
            'tipo' => ['required', 'string', 'max:80'],
            'ubicacion' => ['required', 'string', 'max:160'],
            'estado' => ['required', 'in:'.implode(',', array_column(EstadoRecurso::cases(), 'value'))],
            'descripcion' => ['nullable', 'string'],
        ]);

        $recurso->update($data);

        return redirect()->route('recursos.index')->with('success', 'Recurso actualizado.');
    }

    public function destroy(Recurso $recurso): RedirectResponse
    {
        $this->authorizeStaff();
        $recurso->delete();

        return redirect()->route('recursos.index')->with('success', 'Recurso eliminado.');
    }

    private function authorizeStaff(): void
    {
        if (! auth()->user()?->isStaff()) {
            abort(403);
        }
    }
}
