<?php

namespace App\Livewire\Permission;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class PermissionIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $name;
    public $permission_id;
    public $modalFormVisible = false;
    public $modalViewVisible = false;
    public $modalConfirmDelete = false;

    protected $paginationTheme = 'tailwind';

    // Reinicia la paginación al cambiar la búsqueda
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Reglas de validación
    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions', 'name')->ignore($this->permission_id)],
        ];
    }

    // Abrir modal para crear
    public function create()
    {
        $this->resetInput();
        $this->modalFormVisible = true;
    }

    // Guardar permiso
    public function store()
    {
        $this->validate();

        Permission::create(['name' => $this->name]);

        $this->modalFormVisible = false;
        $this->resetInput();

        session()->flash('message', 'Permiso creado correctamente.');
    }

    // Abrir modal para editar
    public function edit($id)
    {
        $this->resetValidation();
        $this->resetInput();
        $this->permission_id = $id;

        $permission = Permission::findOrFail($id);
        $this->name = $permission->name;

        $this->modalFormVisible = true;
    }

    // Actualizar permiso
    public function update()
    {
        $this->validate();

        $permission = Permission::findOrFail($this->permission_id);
        $permission->update(['name' => $this->name]);

        $this->modalFormVisible = false;
        $this->resetInput();

        session()->flash('message', 'Permiso actualizado correctamente.');
    }

    // Ver permiso
    public function view($id)
    {
        $permission = Permission::findOrFail($id);
        $this->name = $permission->name;
        $this->modalViewVisible = true;
    }

    // Confirmar eliminación
    public function confirmDelete($id)
    {
        $this->permission_id = $id;
        $this->modalConfirmDelete = true;
    }

    // Eliminar permiso
    public function delete()
    {
        Permission::destroy($this->permission_id);
        $this->modalConfirmDelete = false;
        $this->resetInput();

        session()->flash('message', 'Permiso eliminado correctamente.');
    }

    // Resetear los inputs
    private function resetInput()
    {
        $this->reset(['name', 'permission_id']);
        $this->resetValidation();
    }

    // Renderizar vista
    public function render()
    {
        $query = Permission::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return view('livewire.permission.permission-index', [
            'permissions' => $query->orderBy('id', 'desc')->paginate(5),
        ]);
    }
}
