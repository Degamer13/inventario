<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleRequisicion extends Model
{
    use HasFactory;

    protected $table = 'detalles_requisicion';

    protected $fillable = [
        'requisicion_id',
        'item',
        'cantidad',
        'unidad_medida',
        'descripcion',
        'certificado_calidad',
        'proveedor_sugerido',
        'oc',
        'observaciones',
        'existe_inventario',
        'material_id',
        'stock_verificado',
        'observacion_verificacion',
        'entregado',
        'entregado_at',
        'entregado_por_id',
        'cantidad_entregada',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'decimal:2',
            'certificado_calidad' => 'boolean',
            'existe_inventario' => 'boolean',
            'entregado' => 'boolean',
            'entregado_at' => 'datetime',
            'cantidad_entregada' => 'decimal:2',
        ];
    }

    /**
     * Cantidad que queda pendiente por entregar de este renglón.
     */
    public function getCantidadPendienteAttribute(): float
    {
        $entregada = (float) ($this->cantidad_entregada ?? 0);
        return max(0, (float) $this->cantidad - $entregada);
    }

    /**
     * El detalle pertenece a una requisición.
     */
    public function requisicion(): BelongsTo
    {
        return $this->belongsTo(Requisicion::class, 'requisicion_id');
    }

    /**
     * Material del inventario vinculado durante la verificación.
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    /**
     * Usuario que marcó la entrega del renglón.
     */
    public function entregadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entregado_por_id');
    }
}
