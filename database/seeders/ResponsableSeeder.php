<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Responsable;

class ResponsableSeeder extends Seeder
{
    public function run()
    {
        // Crear 50 registros de prueba
        Responsable::factory()->count(50)->create();

        $this->command->info('¡50 responsables de prueba creados!');
    }
}

