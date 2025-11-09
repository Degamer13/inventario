<?php

// database/migrations/XXXX_XX_XX_XXXXXX_create_detalles_salida_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detalles_salida', function (Blueprint $table) {
            $table->id();

            // Clave foránea a la tabla de Salidas
            $table->foreignId('salida_id')
                  ->constrained('salidas')
                  ->onDelete('cascade'); // Si se elimina la salida, se eliminan los detalles

            // Identificación del ítem de inventario
            $table->string('item_tipo', 50)->comment('Tipo de tabla de origen (ej: material, vehiculo)');
            $table->bigInteger('item_id')->nullable()->comment('ID del activo original (opcional)');

            // Clave única de rastreo (Serial, Placa, Código, etc.)
            $table->string('item_serial_placa')->nullable()->comment('Serial, Placa o Identificador único del ítem');

            // Información del ítem en la salida
            $table->string('descripcion');
            $table->integer('cantidad_salida');
            $table->string('unidad_medida')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_salida');
    }
};
