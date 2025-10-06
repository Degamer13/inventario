<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facilidad extends Model
{
    // Nombre de la tabla
    protected $table = 'facilidades';

    // Columnas que se pueden asignar de forma masiva
    protected $fillable = [
        'descripcion',
        'serial',
        'marca',
        'cantidad',
        'ubicacion',
    ];
}
