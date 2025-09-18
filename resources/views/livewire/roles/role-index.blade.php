<div class="p-6 bg-gray-50 dark:bg-gray-900 min-h-screen text-gray-900 dark:text-gray-100">

    {{-- Buscador y Botón Nuevo --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-2 md:space-y-0">
        <div class="flex w-full md:w-1/2 space-x-2">
            <input type="text" wire:model="search" placeholder="Buscar por nombre..."
                class="w-full rounded border border-gray-300 dark:border-gray-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"/>
            <button wire:click="$refresh" class="flex items-center px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 dark:hover:bg-gray-700">       <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16.65 16.65a7.5 7.5 0 111.414-1.414l4.35 4.35z" />
                </svg>Buscar</button>
        </div>
        @can('create role')
            <button wire:click="create" class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 dark:hover:bg-indigo-500 md:ml-2">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>   Nuevo Rol
            </button>
        @endcan
    </div>

    {{-- Tabla de Roles --}}
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded shadow">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Rol</th>

                    <th class="px-4 py-2 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($roles as $role)
                    <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-2">{{ $role->name }}</td>

                        <td class="px-4 py-2 text-right">
                            <div class="flex justify-end space-x-1">
                                @can('show role')
                                    <button wire:click="view({{ $role->id }})" class="inline-flex items-center px-2 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg></button>
                                @endcan
                                @can('edit role')
                                    <button wire:click="edit({{ $role->id }})" class="inline-flex items-center px-2 py-1 text-sm bg-yellow-500 text-white rounded hover:bg-yellow-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z" />
                                        </svg></button>
                                @endcan
                                @can('delete role')
                                    <button wire:click="confirmDelete({{ $role->id }})" class="inline-flex items-center px-2 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg></button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">No hay roles registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $roles->links() }}
        </div>
    </div>

    {{-- Modal Crear/Editar --}}
    @if($modalFormVisible)
        <div class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70"></div>
            <div class="bg-white dark:bg-gray-800 rounded shadow-lg p-6 w-full max-w-lg z-10">
                <h2 class="text-lg font-bold mb-4">{{ $role_id ? 'Editar Rol' : 'Nuevo Rol' }}</h2>
                <div class="space-y-3">
                    <input type="text" wire:model="name" placeholder="Nombre del rol" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">Permisos</label>
                        <select wire:model="permissions" multiple class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            @foreach($allPermissions as $permission)
                                <option value="{{ $permission }}">{{ $permission }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex justify-end space-x-2">
                    <button wire:click="$set('modalFormVisible', false)" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded">Cancelar</button>
                    <button wire:click="{{ $role_id ? 'update' : 'store' }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Guardar</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Ver --}}
    @if($modalViewVisible)
        <div class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70"></div>
            <div class="bg-white dark:bg-gray-800 rounded shadow-lg p-6 w-full max-w-lg z-10">
                <h2 class="text-lg font-bold mb-4">Detalles del Rol</h2>
                <p><strong>Nombre:</strong> {{ $name }}</p>
                <p><strong>Permisos:</strong>
                    @foreach($permissions as $p)
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 mr-1">{{ $p }}</span>
                    @endforeach
                </p>
                <div class="mt-4 text-right">
                    <button wire:click="$set('modalViewVisible', false)" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded">Cerrar</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Confirmar Eliminación --}}
    @if($modalConfirmDelete)
        <div class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70"></div>
            <div class="bg-white dark:bg-gray-800 rounded shadow-lg p-6 w-full max-w-sm z-10">
                <h2 class="text-lg font-bold mb-4">Eliminar Rol</h2>
                <p>¿Estás seguro de eliminar este rol? Esta acción no se puede deshacer.</p>
                <div class="mt-4 flex justify-end space-x-2">
                    <button wire:click="$set('modalConfirmDelete', false)" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded">Cancelar</button>
                    <button wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
                </div>
            </div>
        </div>
    @endif

</div>

{{-- SweetAlert --}}
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('swal', e => {
            Swal.fire({
                title: e.title,
                text: e.text,
                icon: e.icon
            });
        });
    });
</script>
