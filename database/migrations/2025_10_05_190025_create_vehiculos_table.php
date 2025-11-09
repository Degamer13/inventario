<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->string('tipo');
            $table->string('marca');
            $table->string('observacion');
            $table->string('placa')->unique();
            $table->integer('ano');
            $table->string('color');
            $table->string('bateria');
            $table->string('ubicacion');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('vehiculos');
    }
};
