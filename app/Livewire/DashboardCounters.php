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
    }

    public function render()
    {
        return view('livewire.dashboard-counters');
    }
}
