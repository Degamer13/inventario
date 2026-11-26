<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Control de entrega por renglón de las requisiciones verificadas:
     * procura y administrador marcan qué se ENTREGÓ y qué queda PENDIENTE.
     */
    public function up(): void
    {
        Schema::table('detalles_requisicion', function (Blueprint $table) {
            $table->boolean('entregado')
                  ->default(false)
                  ->after('observacion_verificacion')
                  ->comment('true = entregado, false = pendiente de entrega');

            $table->timestamp('entregado_at')
                  ->nullable()
                  ->after('entregado');

            $table->foreignId('entregado_por_id')
                  ->nullable()
                  ->after('entregado_at')
                  ->constrained('users')
                  ->comment('Usuario que marcó la entrega');
        });
    }

    public function down(): void
    {
        Schema::table('detalles_requisicion', function (Blueprint $table) {
            $table->dropConstrainedForeignId('entregado_por_id');
            $table->dropColumn(['entregado', 'entregado_at']);
        });
    }
};
