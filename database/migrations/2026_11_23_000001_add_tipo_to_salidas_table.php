<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega el campo "tipo" a las notas de salida para indicar
     * si la salida corresponde a FABWELD o a SARCOS PORRAS.
     */
    public function up(): void
    {
        Schema::table('salidas', function (Blueprint $table) {
            $table->string('tipo', 30)
                  ->nullable()
                  ->after('n_control')
                  ->index()
                  ->comment('fabweld | sarcos_porras');
        });
    }

    public function down(): void
    {
        Schema::table('salidas', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
