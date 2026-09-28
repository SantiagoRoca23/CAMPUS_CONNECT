<?php

namespace App\Models;

use App\Enums\EstadoSolicitud;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Seguimiento extends Model
{
    use HasFactory;

    protected $table = 'seguimientos';

    protected $fillable = [
        'solicitud_id',
        'user_id',
        'estado_anterior',
        'estado_nuevo',
        'nota',
    ];

    protected function casts(): array
    {
        return [
            'estado_anterior' => EstadoSolicitud::class,
            'estado_nuevo' => EstadoSolicitud::class,
        ];
    }

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
