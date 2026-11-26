<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega el campo de observaciones de procura/verificación a cada renglón
     * de la requisición: el analista de procura anota qué HAY y qué NO HAY
     * en el inventario para cada ítem.
     */
    public function up(): void
    {
        Schema::table('detalles_requisicion', function (Blueprint $table) {
            $table->text('observacion_verificacion')
                  ->nullable()
                  ->after('stock_verificado')
                  ->comment('Observaciones de procura: qué hay y qué no hay en inventario');
        });
    }

    public function down(): void
    {
        Schema::table('detalles_requisicion', function (Blueprint $table) {
            $table->dropColumn('observacion_verificacion');
        });
    }
};
