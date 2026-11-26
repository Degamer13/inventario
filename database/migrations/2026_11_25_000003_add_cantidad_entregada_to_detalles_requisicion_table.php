<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cantidad entregada por renglón: procura y administrador anotan cuánto
     * entregan y el sistema calcula automáticamente lo que queda pendiente.
     */
    public function up(): void
    {
        Schema::table('detalles_requisicion', function (Blueprint $table) {
            $table->decimal('cantidad_entregada', 12, 2)
                  ->nullable()
                  ->after('entregado')
                  ->comment('Cantidad entregada de este renglón');
        });
    }

    public function down(): void
    {
        Schema::table('detalles_requisicion', function (Blueprint $table) {
            $table->dropColumn('cantidad_entregada');
        });
    }
};
