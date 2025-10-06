<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{

   // Nombre de la tabla en español
    protected $table = 'materiales';
    protected $fillable = [
        'descripcion',
        'serial',
        'marca',
        'cantidad',
        'unidad_medida',
        'ubicacion',
    ];


}
