<?php

// database/migrations/XXXX_XX_XX_XXXXXX_create_salidas_table.php

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
        Schema::create('salidas', function (Blueprint $table) {
            $table->id();
            $table->string('proyecto')->nullable();
            $table->integer('ano')->nullable();

            // Campos clave de la salida
            $table->string('n_control')->unique(); // Requisito de unicidad
            $table->date('fecha');

            $table->string('origen')->nullable();
            $table->string('destino')->nullable();
            $table->text('observaciones')->nullable();

            // Claves foráneas para Responsables
            $table->foreignId('entregado_por_id')
                  ->constrained('responsables') // Asume que existe la tabla 'responsables'
                  ->comment('ID del responsable que entrega');

            $table->foreignId('recibido_por_id')
                  ->constrained('responsables')
                  ->comment('ID del responsable que recibe');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salidas');
    }
};
