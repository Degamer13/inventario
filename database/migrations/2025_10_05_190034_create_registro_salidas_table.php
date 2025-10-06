<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('registro_salidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('responsable_id')->constrained('responsables')->onDelete('cascade');
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('registro_salidas');
    }
};
