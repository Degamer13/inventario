<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaquinariaFija extends Model
{
    // Nombre de la tabla
    protected $table = 'maquinarias_fijas';

    // Columnas que se pueden asignar de forma masiva
    protected $fillable = [
        'descripcion',
        'modelo',
        'color',
        'marca',
        'serial',
        'codigo',
        'cantidad',
        'ubicacion',
    ];
}
