<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('maquinarias_fijas', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->string('modelo')->nullable();
            $table->string('color')->nullable();
            $table->string('marca')->nullable();
            $table->string('serial')->nullable();
            $table->string('codigo')->nullable();
            $table->integer('cantidad')->default(0);
            $table->string('ubicacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('maquinarias_fijas');
    }
};
