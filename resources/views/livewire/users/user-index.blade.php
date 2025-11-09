<div class="p-6 bg-gray-50 dark:bg-neutral-700 min-h-screen text-gray-900 dark:text-gray-100">
    {{-- Buscador y botones --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-2 md:space-y-0">
        <div class="flex w-full md:w-1/2 space-x-2">
            <input type="text" wire:model="search" placeholder="Buscar por nombre o email..."
                class="w-full rounded border border-gray-300 dark:border-neutral-600 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100"/>
            <button wire:click="$refresh"
                class="flex items-center px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 dark:hover:bg-gray-700" >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16.65 16.65a7.5 7.5 0 111.414-1.414l4.35 4.35z" />
                </svg> Buscar
            </button>
        </div>
        @can('create user')
            <button wire:click="create"
                class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 dark:hover:bg-indigo-500 md:ml-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Usuario
            </button>
        @endcan
    </div>

    {{-- Mensaje de éxito o error --}}
    @if ($message)
        <div id="alert-message" class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg mb-4 max-w-xs flex items-center justify-between">
            <p>{{ $message }}</p>
            <button onclick="document.getElementById('alert-message').style.display = 'none';" class="ml-4 bg-transparent text-white font-semibold hover:text-gray-200 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    {{-- Tabla --}}
    <div class="overflow-x-auto rounded shadow bg-white dark:bg-neutral-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
            <thead class="bg-gray-100 dark:bg-neutral-900">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Roles</th>
                    <th class="px-6 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                @forelse($users as $user)
                    <tr class="bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700">
                        <td class="px-4 py-2">{{ $user->name }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2">
                            @foreach($user->getRoleNames() as $role)
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded bg-gray-200 dark:bg-neutral-700 text-gray-800 dark:text-gray-200 mr-1">{{ $role }}</span>
                            @endforeach
                        </td>
                        <td class="px-4 py-2 text-right">
                            <div class="flex justify-end space-x-1">
                                @can('show user')
                                    <button wire:click="view({{ $user->id }})" class="inline-flex items-center px-2 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600" title="Ver">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                @endcan
                                @can('edit user')
                                    <button wire:click="edit({{ $user->id }})" class="inline-flex items-center px-2 py-1 text-sm bg-yellow-500 text-white rounded hover:bg-yellow-600" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z" />
                                        </svg>
                                    </button>
                                @endcan
                                @can('delete user')
                                    <button wire:click="confirmDelete({{ $user->id }})" class="inline-flex items-center px-2 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">No hay usuarios registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $users->links() }}
        </div>
    </div>

    {{-- Modal Crear/Editar --}}
    @if($modalFormVisible)
        <div class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70"></div>
            <div class="bg-white dark:bg-neutral-800 rounded shadow-lg p-6 w-full max-w-lg z-10">
                <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">
                    {{ $user_id ? 'Editar Usuario' : 'Nuevo Usuario' }}
                </h2>

                <div class="space-y-3">
                    {{-- Nombre --}}
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="name" required
                            placeholder="Nombre"
                            class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2
                                   bg-white dark:bg-neutral-700 text-gray-900 dark:text-gray-100">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">Campo requerido</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" wire:model="email" required
                            placeholder="Email"
                            class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2
                                   bg-white dark:bg-neutral-700 text-gray-900 dark:text-gray-100">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">Campo requerido</p>
                        @enderror
                    </div>

                    {{-- Contraseña --}}
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">
                            Contraseña <span class="text-red-500">*</span>
                        </label>
                        <input type="password" wire:model="password" required
                            placeholder="Contraseña"
                            class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2
                                   bg-white dark:bg-neutral-700 text-gray-900 dark:text-gray-100">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">Campo requerido</p>
                        @enderror
                    </div>

                    {{-- Confirmar Contraseña --}}
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">
                            Confirmar Contraseña <span class="text-red-500">*</span>
                        </label>
                        <input type="password" wire:model="password_confirmation" required
                            placeholder="Confirmar Contraseña"
                            class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2
                                   bg-white dark:bg-neutral-700 text-gray-900 dark:text-gray-100">
                        @error('password_confirmation')
                            <p class="text-red-500 text-sm mt-1">Campo requerido</p>
                        @enderror
                    </div>

                    {{-- Roles --}}
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">
                            Roles <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="roles" multiple required
                            class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2
                                   bg-white dark:bg-neutral-700 text-gray-900 dark:text-gray-100">
                            @foreach($allRoles as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                        </select>
                        @error('roles')
                            <p class="text-red-500 text-sm mt-1">Campo requerido</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 flex justify-end space-x-2">
                    <button wire:click="$set('modalFormVisible', false)"
                        class="px-4 py-2 bg-gray-300 dark:bg-neutral-600 text-gray-700 dark:text-gray-200 rounded
                               hover:bg-gray-400 dark:hover:bg-neutral-500">
                        Cancelar
                    </button>
                    <button wire:click="{{ $user_id ? 'update' : 'store' }}"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 dark:hover:bg-indigo-500">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Ver --}}
    @if($modalViewVisible)
        <div class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70"></div>
            <div class="bg-white dark:bg-neutral-800 rounded shadow-lg p-6 w-full max-w-lg z-10">
                <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Detalles del Usuario</h2>
                <div class="space-y-2 text-gray-900 dark:text-gray-100">
                    <p><strong>Nombre:</strong> {{ $name }}</p>
                    <p><strong>Email:</strong> {{ $email }}</p>
                    <p><strong>Roles:</strong>
                        @foreach($roles as $role)
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded bg-gray-200 dark:bg-neutral-700 text-gray-800 dark:text-gray-200 mr-1">{{ $role }}</span>
                        @endforeach
                    </p>
                </div>
                <div class="mt-4 text-right">
                    <button wire:click="$set('modalViewVisible', false)" class="px-4 py-2 bg-gray-300 dark:bg-neutral-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-neutral-500">Cerrar</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Confirmar Eliminación --}}
    @if($modalConfirmDelete)
        <div class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70"></div>
            <div class="bg-white dark:bg-neutral-800 rounded shadow-lg p-6 w-full max-w-sm z-10">
                <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Eliminar Usuario</h2>
                <p class="mb-4 text-gray-900 dark:text-gray-100">¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.</p>
                <div class="flex justify-end space-x-2">
                    <button wire:click="$set('modalConfirmDelete', false)" class="px-4 py-2 bg-gray-300 dark:bg-neutral-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-neutral-500">Cancelar</button>
                    <button wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 dark:hover:bg-red-500">Eliminar</button>
                </div>
            </div>
        </div>
    @endif
</div>
