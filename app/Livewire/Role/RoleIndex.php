<?php

namespace App\Livewire\Role;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class RoleIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $name;
    public $permissions = [];
    public $role_id;
    public $modalFormVisible = false;
    public $modalViewVisible = false;
    public $modalConfirmDelete = false;

    protected $paginationTheme = 'tailwind';

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($this->role_id), // Validación de nombre único
            ],
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,name',
        ];
    }

    // Abrir modal para crear
    public function create()
    {
        $this->resetInput();
        $this->modalFormVisible = true;
    }

    // Guardar rol
    public function store()
    {
        $this->validate();

        $role = Role::create(['name' => $this->name]);
        $role->syncPermissions($this->permissions);

        $this->modalFormVisible = false;
        $this->resetInput();

        session()->flash('message', 'Rol creado correctamente.');
    }

    // Abrir modal para editar
    public function edit($id)
    {
        $this->resetValidation();
        $this->resetInput();

        $this->role_id = $id;
        $role = Role::with('permissions')->findOrFail($id);

        $this->name = $role->name;
        $this->permissions = $role->permissions->pluck('name')->toArray();

        $this->modalFormVisible = true;
    }

    // Actualizar rol
    public function update()
    {
        $this->validate();

        $role = Role::findOrFail($this->role_id);
        $role->update(['name' => $this->name]);
        $role->syncPermissions($this->permissions);

        $this->modalFormVisible = false;
        $this->resetInput();

        session()->flash('message', 'Rol actualizado correctamente.');
    }

    // Ver rol
    public function view($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        $this->name = $role->name;
        $this->permissions = $role->permissions->pluck('name')->toArray();

        $this->modalViewVisible = true;
    }

    // Confirmar eliminación
    public function confirmDelete($id)
    {
        $this->role_id = $id;
        $this->modalConfirmDelete = true;
    }

    // Eliminar rol
    public function delete()
    {
        Role::destroy($this->role_id);
        $this->modalConfirmDelete = false;
        $this->resetInput();

        session()->flash('message', 'Rol eliminado correctamente.');
    }

    // Resetear inputs
    private function resetInput()
    {
        $this->reset(['name','permissions','role_id']);
        $this->resetValidation();
    }

    public function render()
    {
        $query = Role::with('permissions');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return view('livewire.roles.role-index', [
            'roles' => $query->orderBy('id', 'desc')->paginate(5),
            'allPermissions' => Permission::pluck('name')->toArray(),
        ]);
    }
}
