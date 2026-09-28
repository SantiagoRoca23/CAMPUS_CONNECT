<?php

namespace App\Models;

use App\Enums\EstadoRecurso;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recurso extends Model
{
    use HasFactory;

    protected $table = 'recursos';

    protected $fillable = [
        'codigo',
        'nombre',
        'tipo',
        'ubicacion',
        'estado',
        'descripcion',
    ];

    protected function casts(): array
    {
        return [
            'estado' => EstadoRecurso::class,
        ];
    }

    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class);
    }
}
