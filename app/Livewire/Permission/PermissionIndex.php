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

    protected function rules()
    {
        return [
            'name' => ['required','string','max:255', Rule::unique('permissions','name')->ignore($this->permission_id)],
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

        $this->dispatch('swal', [
            'title' => '¡Permiso creado!',
            'text' => 'El permiso se ha registrado correctamente.',
            'icon' => 'success'
        ]);
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

        $this->dispatch('swal', [
            'title' => '¡Permiso actualizado!',
            'text' => 'El permiso se ha actualizado correctamente.',
            'icon' => 'success'
        ]);
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

        $this->dispatch('swal', [
            'title' => '¡Permiso eliminado!',
            'text' => 'El permiso se ha eliminado correctamente.',
            'icon' => 'success'
        ]);
    }

    private function resetInput()
    {
        $this->reset(['name','permission_id']);
        $this->resetValidation();
    }

    public function render()
    {
        $query = Permission::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return view('livewire.permission.permission-index', [
            'permissions' => $query->orderBy('id','desc')->paginate(5),
        ]);
    }
}
