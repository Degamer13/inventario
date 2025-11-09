<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleSalida extends Model
{
    use HasFactory;

    protected $table = 'detalles_salida';

    protected $fillable = [
        'salida_id',
        'item_tipo',
        'item_id',
        'item_serial_placa',
        'descripcion',
        'cantidad_salida',
        'unidad_medida',
    ];

    /**
     * Define la relación: El Detalle pertenece a una Salida.
     */
    public function salida(): BelongsTo
    {
        return $this->belongsTo(Salida::class);
    }
}
