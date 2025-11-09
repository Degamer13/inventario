<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $name, $email, $password, $password_confirmation;
    public $roles = [];
    public $user_id;
    public $modalFormVisible = false;
    public $modalViewVisible = false;
    public $modalConfirmDelete = false;

    public $message = ''; // Para mostrar alertas de éxito o error
    protected $paginationTheme = 'tailwind';

    // 🔹 Resetear paginación al actualizar el buscador
    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'name')->ignore($this->user_id), // Validación de nombre único
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user_id) // Validación de correo único
            ],
            'password' => $this->user_id
                ? 'nullable|string|min:6|confirmed'
                : 'required|string|min:6|confirmed',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
        ];
    }

    public function create()
    {
        $this->resetInput();
        $this->modalFormVisible = true;
    }

    public function store()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
        ]);

        $user->syncRoles($this->roles);

        $this->modalFormVisible = false;
        $this->resetInput();

        $this->message = 'Usuario registrado correctamente.'; // Mensaje de éxito
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->resetInput();

        $this->user_id = $id;
        $user = User::with('roles')->findOrFail($id);

        $this->name = $user->name;
        $this->email = $user->email;
        $this->roles = $user->roles->pluck('name')->toArray();

        $this->modalFormVisible = true;
    }

    public function update()
    {
        $this->validate();

        $user = User::findOrFail($this->user_id);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $data['password'] = bcrypt($this->password);
        }

        $user->update($data);
        $user->syncRoles($this->roles);

        $this->modalFormVisible = false;
        $this->resetInput();

        $this->message = 'Usuario actualizado correctamente.'; // Mensaje de éxito
    }

    public function view($id)
    {
        $user = User::with('roles')->findOrFail($id);

        $this->name = $user->name;
        $this->email = $user->email;
        $this->roles = $user->roles->pluck('name')->toArray();

        $this->modalViewVisible = true;
    }

    public function confirmDelete($id)
    {
        $this->user_id = $id;
        $this->modalConfirmDelete = true;
    }

    public function delete()
    {
        User::destroy($this->user_id);
        $this->modalConfirmDelete = false;
        $this->resetInput();

        $this->message = 'Usuario eliminado correctamente.'; // Mensaje de éxito
    }

    private function resetInput()
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'roles', 'user_id']);
        $this->resetValidation();
    }

    public function render()
    {
        $query = User::with('roles');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.users.user-index', [
            'users' => $query->orderBy('id', 'desc')->paginate(5),
            'allRoles' => Role::pluck('name')->toArray(),
        ]);
    }
}
