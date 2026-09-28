<?php

namespace App\Models;

use App\Enums\EstadoSolicitud;
use App\Enums\PrioridadSolicitud;
use App\Enums\TipoSolicitud;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'codigo',
        'titulo',
        'descripcion',
        'tipo',
        'prioridad',
        'estado',
        'ubicacion',
        'user_id',
        'asignado_a',
        'recurso_id',
        'cerrada_at',
    ];

    protected function casts(): array
    {
        return [
            'tipo' => TipoSolicitud::class,
            'prioridad' => PrioridadSolicitud::class,
            'estado' => EstadoSolicitud::class,
            'cerrada_at' => 'datetime',
        ];
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    public function recurso(): BelongsTo
    {
        return $this->belongsTo(Recurso::class);
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(Evidencia::class);
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentario::class);
    }

    public function seguimientos(): HasMany
    {
        return $this->hasMany(Seguimiento::class);
    }

    public static function generarCodigo(): string
    {
        $secuencia = str_pad((string) ((self::max('id') ?? 0) + 1), 5, '0', STR_PAD_LEFT);

        return 'SC-'.now()->format('Y').'-'.$secuencia;
    }
}
