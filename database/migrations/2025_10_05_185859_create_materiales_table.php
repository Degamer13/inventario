<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('materiales', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->string('serial')->unique();
            $table->string('marca');
            $table->integer('cantidad');
            $table->string('unidad_medida');
            $table->string('ubicacion');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('materiales');
    }
};
