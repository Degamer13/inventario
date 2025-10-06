<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('materiales', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->string('serial')->nullable();
            $table->string('marca')->nullable();
            $table->integer('cantidad')->default(0);
            $table->string('unidad_medida')->nullable();
            $table->string('ubicacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('materiales');
    }
};
