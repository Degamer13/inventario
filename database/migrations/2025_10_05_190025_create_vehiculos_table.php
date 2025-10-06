<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->string('tipo')->nullable();
            $table->string('marca')->nullable();
            $table->string('observacion')->nullable();
            $table->string('placa')->nullable();
            $table->integer('ano')->nullable();
            $table->string('color')->nullable();
            $table->string('bateria')->nullable();
            $table->string('ubicacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('vehiculos');
    }
};
