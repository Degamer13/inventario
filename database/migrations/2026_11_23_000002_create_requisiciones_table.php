<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla de cabecera de las requisiciones de procura.
     * Tipo: fabweld | sarcos_porras
     * Prioridad: urgente | intermedio | bajo
     * Estado: pendiente | verificada | aprobada | rechazada
     */
    public function up(): void
    {
        Schema::create('requisiciones', function (Blueprint $table) {
            $table->id();

            // Tipo de requisición: FABWELD o SARCOS PORRAS
            $table->string('tipo', 30)->comment('fabweld | sarcos_porras');

            // Número de control RQ-YYYY-AA-CC-Z (se genera automáticamente)
            $table->string('n_requisicion')->unique();

            // Datos de la requisición (según formato 00-PC-F-01)
            $table->string('proyecto')->nullable();
            $table->date('fecha');
            $table->string('centro_costos')->nullable();
            $table->string('unidad_solicitante')->nullable();

            // Prioridad: urgente, intermedio, bajo
            $table->string('prioridad', 20)->default('intermedio')->comment('urgente | intermedio | bajo');

            // Flujo: pendiente -> verificada (admin) -> aprobada/rechazada (planificador)
            $table->string('estado', 20)->default('pendiente')->comment('pendiente | verificada | aprobada | rechazada');

            $table->text('observaciones')->nullable();

            // Quién preparó / verificó / revisó la requisición
            $table->foreignId('preparado_por_id')->constrained('users')->comment('Usuario que registra la requisición');
            $table->foreignId('verificado_por_id')->nullable()->constrained('users')->comment('Administrador que verifica contra inventario');
            $table->foreignId('revisado_por_id')->nullable()->constrained('users')->comment('Planificador que aprueba o rechaza');

            // Marcas de tiempo del flujo
            $table->timestamp('verificada_at')->nullable();
            $table->timestamp('aprobada_at')->nullable();
            $table->timestamp('rechazada_at')->nullable();
            $table->text('motivo_rechazo')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisiciones');
    }
};
