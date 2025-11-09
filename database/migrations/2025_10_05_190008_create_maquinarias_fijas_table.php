<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('maquinarias_fijas', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->string('modelo');
            $table->string('color');
            $table->string('marca');
            $table->string('serial')->unique();
            $table->string('codigo')->unique();
            $table->integer('cantidad');
            $table->string('ubicacion');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('maquinarias_fijas');
    }
};
