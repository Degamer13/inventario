<div class="p-6 bg-gray-50 dark:bg-gray-900 min-h-screen text-gray-900 dark:text-gray-100">

    {{-- Buscador y botones --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-2 md:space-y-0">
        <div class="flex w-full md:w-1/2 space-x-2">
            <input
                type="text"
                wire:model.debounce.500ms="search"
                placeholder="Buscar por nombre o cédula..."
                class="w-full rounded border border-gray-300 dark:border-gray-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
            />
            <button wire:click="$refresh" class="flex items-center px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 dark:hover:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16.65 16.65a7.5 7.5 0 111.414-1.414l4.35 4.35z" />
                </svg>
                Buscar
            </button>
        </div>

        {{-- Botón Nuevo Responsable --}}
        @can('create responsable')
            <button wire:click="create" class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 dark:hover:bg-indigo-500 md:ml-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Responsable
            </button>
        @endcan
    </div>

    {{-- Tabla --}}
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded shadow">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Nombre</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Cédula</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Email</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Teléfono</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Cargo</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Área</th>
                    <th class="px-4 py-2 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($responsables as $r)
                    <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-2">{{ $r->name }}</td>
                        <td class="px-4 py-2">{{ $r->cedula }}</td>
                        <td class="px-4 py-2">{{ $r->email }}</td>
                        <td class="px-4 py-2">{{ $r->telefono }}</td>
                        <td class="px-4 py-2">{{ $r->cargo }}</td>
                        <td class="px-4 py-2">{{ $r->area }}</td>
                        <td class="px-4 py-2 text-right">
                            <div class="flex justify-end space-x-1">
                                {{-- Ver --}}
                                @can('show responsable')
                                    <button wire:click="view({{ $r->id }})"
                                            class="inline-flex items-center px-2 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600"
                                            title="Ver">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                @endcan

                                {{-- Editar --}}
                                @can('edit responsable')
                                    <button wire:click="edit({{ $r->id }})"
                                            class="inline-flex items-center px-2 py-1 text-sm bg-yellow-500 text-white rounded hover:bg-yellow-600"
                                            title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z" />
                                        </svg>
                                    </button>
                                @endcan

                                {{-- Eliminar --}}
                                @can('delete responsable')
                                    <button wire:click="confirmDelete({{ $r->id }})"
                                            class="inline-flex items-center px-2 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700"
                                            title="Eliminar">
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
                        <td colspan="7" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">No hay resultados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="mt-4">
        {{ $responsables->links() }}
    </div>
{{-- Modal Crear/Editar --}}
@if($modalFormVisible)
<div class="fixed inset-0 flex items-center justify-center z-50">
    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70"></div>
    <div class="bg-white dark:bg-gray-800 rounded shadow-lg p-6 w-full max-w-lg z-10">
        <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">{{ $responsable_id ? 'Editar Responsable' : 'Nuevo Responsable' }}</h2>
        <div class="space-y-3">
            <input type="text" wire:model="name" placeholder="Nombre" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            <input type="text" wire:model="cedula" placeholder="Cédula" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            <input type="email" wire:model="email" placeholder="Email" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            <input type="text" wire:model="telefono" placeholder="Teléfono" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            <input type="text" wire:model="cargo" placeholder="Cargo" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            <input type="text" wire:model="area" placeholder="Área" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
        </div>
        <div class="mt-4 flex justify-end space-x-2">
            <button wire:click="$set('modalFormVisible', false)" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500">Cancelar</button>
            <button wire:click="{{ $responsable_id ? 'update' : 'store' }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 dark:hover:bg-indigo-500">Guardar</button>
        </div>
    </div>
</div>
@endif

{{-- Modal Ver --}}
@if($modalViewVisible)
<div class="fixed inset-0 flex items-center justify-center z-50">
    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70"></div>
    <div class="bg-white dark:bg-gray-800 rounded shadow-lg p-6 w-full max-w-lg z-10">
        <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Detalles del Responsable</h2>
        <div class="space-y-2 text-gray-900 dark:text-gray-100">
            <p><strong>Nombre:</strong> {{ $name }}</p>
            <p><strong>Cédula:</strong> {{ $cedula }}</p>
            <p><strong>Email:</strong> {{ $email }}</p>
            <p><strong>Teléfono:</strong> {{ $telefono }}</p>
            <p><strong>Cargo:</strong> {{ $cargo }}</p>
            <p><strong>Área:</strong> {{ $area }}</p>
        </div>
        <div class="mt-4 text-right">
            <button wire:click="$set('modalViewVisible', false)" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500">Cerrar</button>
        </div>
    </div>
</div>
@endif

{{-- Modal Confirmar Eliminación --}}
@if($modalConfirmDelete)
<div class="fixed inset-0 flex items-center justify-center z-50">
    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70"></div>
    <div class="bg-white dark:bg-gray-800 rounded shadow-lg p-6 w-full max-w-sm z-10">
        <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Eliminar Responsable</h2>
        <p class="mb-4 text-gray-900 dark:text-gray-100">¿Estás seguro de que deseas eliminar este responsable? Esta acción no se puede deshacer.</p>
        <div class="flex justify-end space-x-2">
            <button wire:click="$set('modalConfirmDelete', false)" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500">Cancelar</button>
            <button wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 dark:hover:bg-red-500">Eliminar</button>
        </div>
    </div>
</div>
@endif

</div>
