<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Responsable;

class ResponsableFactory extends Factory
{
    protected $model = Responsable::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'cedula' => $this->faker->unique()->numerify('#########'), // 9 dígitos aleatorios
            'email' => $this->faker->unique()->safeEmail(),
            'telefono' => $this->faker->phoneNumber(),
            'cargo' => $this->faker->jobTitle(),
            'area' => $this->faker->randomElement(['Finanzas', 'Administración', 'Compras', 'TI', 'RRHH', 'Seguridad']),
        ];
    }
}
