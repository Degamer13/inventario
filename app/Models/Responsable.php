<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Responsable extends Model
{
    use HasFactory;
   // Nombre de la tabla en español
    protected $table = 'responsables';
    protected $fillable = [
        'name',
        'cedula',
        'email',
        'telefono',
        'cargo',
        'area',
    ];
}
