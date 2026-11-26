<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Requisicion extends Model
{
    use HasFactory;

    protected $table = 'requisiciones';

    protected $fillable = [
        'tipo',
        'n_requisicion',
        'proyecto',
        'fecha',
        'centro_costos',
        'unidad_solicitante',
        'prioridad',
        'estado',
        'observaciones',
        'preparado_por_id',
        'verificado_por_id',
        'revisado_por_id',
        'verificada_at',
        'aprobada_at',
        'rechazada_at',
        'motivo_rechazo',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'verificada_at' => 'datetime',
            'aprobada_at' => 'datetime',
            'rechazada_at' => 'datetime',
        ];
    }

    // Constantes de apoyo para la interfaz
    public const TIPOS = [
        'fabweld' => 'Fabweld',
        'sarcos_porras' => 'Sarcos Porras',
    ];

    public const PRIORIDADES = ['urgente', 'intermedio', 'bajo'];

    public const ESTADOS = ['pendiente', 'verificada', 'aprobada', 'rechazada'];

    /**
     * Una requisición tiene muchos detalles (renglones).
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleRequisicion::class, 'requisicion_id');
    }

    /**
     * Usuario que registró (preparó) la requisición.
     */
    public function preparadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'preparado_por_id');
    }

    /**
     * Administrador que verificó contra inventario.
     */
    public function verificadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verificado_por_id');
    }

    /**
     * Planificador que aprobó o rechazó.
     */
    public function revisadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisado_por_id');
    }

    // === ACCESORES DE APOYO ===

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? ucfirst((string) $this->tipo);
    }

    public function getPrioridadLabelAttribute(): string
    {
        return ucfirst((string) $this->prioridad);
    }

    public function getEstadoLabelAttribute(): string
    {
        return ucfirst((string) $this->estado);
    }
}
