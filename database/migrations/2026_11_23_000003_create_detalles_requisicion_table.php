<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Detalles (renglones) de cada requisición de procura.
     */
    public function up(): void
    {
        Schema::create('detalles_requisicion', function (Blueprint $table) {
            $table->id();

            $table->foreignId('requisicion_id')
                  ->constrained('requisiciones')
                  ->onDelete('cascade');

            // Número de renglón (ITEM)
            $table->integer('item');

            // Cantidad solicitada
            $table->decimal('cantidad', 12, 2);
            $table->string('unidad_medida')->nullable();

            // Descripción del material, herramienta, equipo y/o servicio
            $table->text('descripcion');

            // ¿Requiere certificado de calidad?
            $table->boolean('certificado_calidad')->default(false);

            // Proveedor sugerido (si aplica)
            $table->string('proveedor_sugerido')->nullable();

            // Orden de compra (la llena el analista/coordinador de procura)
            $table->string('oc')->nullable();

            $table->text('observaciones')->nullable();

            // === VERIFICACIÓN CONTRA INVENTARIO (la realiza el administrador) ===
            // ¿El ítem existe en el inventario del sistema?
            $table->boolean('existe_inventario')->nullable();
            // Material del inventario vinculado (opcional)
            $table->foreignId('material_id')->nullable()->constrained('materiales')->nullOnDelete();
            // Stock observado al momento de la verificación
            $table->integer('stock_verificado')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles_requisicion');
    }
};
