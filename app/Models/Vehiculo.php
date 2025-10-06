<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    // Nombre de la tabla
    protected $table = 'vehiculos';

    // Columnas que se pueden asignar de forma masiva
    protected $fillable = [
        'descripcion',
        'tipo',
        'marca',
        'observacion',
        'placa',
        'ano',
        'color',
        'bateria',
        'ubicacion',
    ];
}
