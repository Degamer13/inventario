<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('detalle_salidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registro_salida_id')->constrained('registro_salidas')->onDelete('cascade');
            $table->enum('tipo_item', ['material','facilidad','maquinaria_fija','sistema','vehiculo']);
            $table->unsignedBigInteger('item_id');
            $table->integer('cantidad')->default(1);
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('detalle_salidas');
    }
};
