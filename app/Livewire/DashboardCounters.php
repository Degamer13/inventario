<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Responsable;

class DashboardCounters extends Component
{
    public $usersCount;
    public $rolesCount;
    public $responsablesCount;

    public function mount()
    {
        $this->usersCount = User::count();
        $this->rolesCount = Role::count();
        $this->responsablesCount = Responsable::count();
    }

    public function render()
    {
        return view('livewire.dashboard-counters');
    }
}
