<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Responsable;
use App\Models\Material;
use App\Models\Facilidad;
use App\Models\MaquinariaFija;
use App\Models\Sistema;
use App\Models\Vehiculo;
use App\Models\Salida;
use App\Models\Requisicion;
use Illuminate\Support\Facades\Auth;

class DashboardCounters extends Component
{
    public $usersCount;
    public $rolesCount;
    public $responsablesCount;
    public $materialesCount;
    public $facilidadesCount;
    public $maquinariasFijasCount;
    public $sistemasCount;
    public $vehiculosCount;
    public $salidasCount;
    public $requisicionesCount;
    public $requisicionesPendientes;
    public $esGestorRequisiciones = false;
    public $pendientesRequisiciones = [];

    public function mount()
    {
        $this->usersCount = User::count();
        $this->rolesCount = Role::count();
        $this->responsablesCount = Responsable::count();
        $this->materialesCount = Material::count();
        $this->facilidadesCount = Facilidad::count();
        $this->maquinariasFijasCount = MaquinariaFija::count();
        $this->sistemasCount = Sistema::count();
        $this->vehiculosCount = Vehiculo::count();
        $this->salidasCount = Salida::count();
        $this->requisicionesCount = Requisicion::count();
        $this->requisicionesPendientes = Requisicion::where('estado', 'pendiente')->count();

        // Administrador, planificador y procura reciben TODAS las requisiciones:
        // aquí ven las pendientes por atender directamente en su dashboard.
        $this->esGestorRequisiciones = Auth::user()
            ->hasAnyRole(['admin', 'planificador', 'procura']);

        $this->pendientesRequisiciones = $this->esGestorRequisiciones
            ? Requisicion::with('preparadoPor')
                ->where('estado', 'pendiente')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
            : collect();
    }

    public function render()
    {
        return view('livewire.dashboard-counters');
    }
}
