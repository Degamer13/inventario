<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sistema extends Model
{
   // Nombre de la tabla
    protected $table = 'sistemas';

    // Columnas que se pueden asignar de forma masiva
    protected $fillable = [
        'descripcion',
        'serial',
        'marca',
        'cantidad',
        'ubicacion',
    ];
}
