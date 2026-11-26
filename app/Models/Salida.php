<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salida extends Model
{
    use HasFactory;

    protected $table = 'salidas';

    protected $fillable = [
        'proyecto',
        'ano',
        'n_control',
        'tipo',
        'fecha',
        'origen',
        'destino',
        'observaciones',
        'entregado_por_id',
        'recibido_por_id',
    ];

    // Constantes de apoyo para la interfaz
    public const TIPOS = [
        'fabweld' => 'Fabweld',
        'sarcos_porras' => 'Sarcos Porras',
    ];

    /**
     * Define la relación: Una Salida tiene muchos Detalles de Salida.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleSalida::class);
    }

    /**
     * Define la relación: La Salida pertenece al Responsable que entrega.
     */
    public function entregadoPor(): BelongsTo
    {
        return $this->belongsTo(Responsable::class, 'entregado_por_id');
    }

    /**
     * Define la relación: La Salida pertenece al Responsable que recibe.
     */
    public function recibidoPor(): BelongsTo
    {
        return $this->belongsTo(Responsable::class, 'recibido_por_id');
    }

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? '—';
    }
}
