<div class="p-6 bg-gray-50 dark:bg-neutral-700 min-h-screen text-gray-900 dark:text-gray-100">

    {{-- Buscador y botones --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-2 md:space-y-0">
        <div class="flex w-full md:w-1/2 space-x-2">
            <input
                type="text"
                wire:model.debounce.500ms="search"
                placeholder="Buscar por descripción, marca o placa..."
                class="w-full rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100"
            />
            <button wire:click="$refresh" class="flex items-center px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 dark:hover:bg-neutral-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16.65 16.65a7.5 7.5 0 111.414-1.414l4.35 4.35z" />
                </svg>
                Buscar
            </button>
        </div>

        <div class="flex items-center space-x-2">
          {{-- Botón Generar PDF --}}
    @can('pdf vehiculo')
    <button wire:click="exportPdf" class="flex items-center px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 dark:hover:bg-red-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
            <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zM6 4h7v4h4v11c0 .55-.45 1-1 1H6c-.55 0-1-.45-1-1V5c0-.55.45-1 1-1zm6 12H9v-2h3c.55 0 1-.45 1-1s-.45-1-1-1h-2v-2h2c.55 0 1-.45 1-1s-.45-1-1-1H9v-2h3c.55 0 1-.45 1-1s-.45-1-1-1H7v2.58l1.41 1.41c.2.2.3.47.3.7s-.1.5-.3.7l-1.41 1.41V16h3c.55 0 1-.45 1-1s-.45-1-1-1z" />
        </svg>
        Generar PDF
    </button>
    @endcan

    {{-- Botón Nuevo Vehículo --}}
    @can('create vehiculo')
    <button wire:click="create" class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 dark:hover:bg-indigo-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nuevo Vehículo
    </button>
    @endcan

        </div>
    </div>

    {{-- Tabla --}}
    <div class="overflow-x-auto rounded shadow bg-white dark:bg-neutral-900">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
            <thead class="bg-gray-100 dark:bg-neutral-950">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Descripción</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Tipo</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Marca</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Observación</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Placa</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Año</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Color</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Batería</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Ubicación</th>
                    <th class="px-4 py-2 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                @forelse($vehiculos as $v)
                    <tr class="bg-white dark:bg-neutral-900 hover:bg-gray-50 dark:hover:bg-neutral-800">
                        <td class="px-4 py-2">{{ $v->descripcion }}</td>
                        <td class="px-4 py-2">{{ $v->tipo }}</td>
                        <td class="px-4 py-2">{{ $v->marca }}</td>
                        <td class="px-4 py-2">{{ $v->observacion }}</td>
                        <td class="px-4 py-2">{{ $v->placa }}</td>
                        <td class="px-4 py-2">{{ $v->ano }}</td>
                        <td class="px-4 py-2">{{ $v->color }}</td>
                        <td class="px-4 py-2">{{ $v->bateria }}</td>
                        <td class="px-4 py-2">{{ $v->ubicacion }}</td>
                        <td class="px-4 py-2 text-right">
                            <div class="flex justify-end space-x-1">
                                 {{-- Ver --}}
        @can('show vehiculo')
        <button wire:click="view({{ $v->id }})"
                class="inline-flex items-center px-2 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600 dark:hover:bg-blue-400"
                title="Ver">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </button>
        @endcan

        {{-- Editar --}}
        @can('edit vehiculo')
        <button wire:click="edit({{ $v->id }})"
                class="inline-flex items-center px-2 py-1 text-sm bg-yellow-500 text-white rounded hover:bg-yellow-600 dark:hover:bg-yellow-400"
                title="Editar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z" />
            </svg>
        </button>
        @endcan

        {{-- Eliminar --}}
        @can('delete vehiculo')
        <button wire:click="confirmDelete({{ $v->id }})"
                class="inline-flex items-center px-2 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700 dark:hover:bg-red-500"
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
                        <td colspan="10" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">No hay resultados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="mt-4">
        {{ $vehiculos->links() }}
    </div>

    {{-- Modal Crear/Editar --}}
    @if($modalFormVisible)
    <div class="fixed inset-0 flex items-center justify-center z-50">
        <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70"></div>
        <div class="rounded shadow-lg p-6 w-full max-w-lg z-10 bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100">
            <h2 class="text-lg font-bold mb-4">{{ $vehiculo_id ? 'Editar Vehículo' : 'Nuevo Vehículo' }}</h2>
            <div class="space-y-3">
                <input type="text" wire:model="descripcion" placeholder="Descripción" class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                <input type="text" wire:model="tipo" placeholder="Tipo" class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                <input type="text" wire:model="marca" placeholder="Marca" class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                <input type="text" wire:model="observacion" placeholder="Observación" class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                <input type="text" wire:model="placa" placeholder="Placa" class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                <input type="number" wire:model="ano" placeholder="Año" class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                <input type="text" wire:model="color" placeholder="Color" class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                <input type="text" wire:model="bateria" placeholder="Batería" class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                <input type="text" wire:model="ubicacion" placeholder="Ubicación" class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
            </div>
            <div class="mt-4 flex justify-end space-x-2">
                <button wire:click="$set('modalFormVisible', false)" class="px-4 py-2 bg-gray-300 dark:bg-neutral-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-neutral-500">Cancelar</button>
                <button wire:click="{{ $vehiculo_id ? 'update' : 'store' }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 dark:hover:bg-indigo-500">Guardar</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Ver --}}
    @if($modalViewVisible)
    <div class="fixed inset-0 flex items-center justify-center z-50">
        <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70"></div>
        <div class="rounded shadow-lg p-6 w-full max-w-lg z-10 bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100">
            <h2 class="text-lg font-bold mb-4">Detalles del Vehículo</h2>
            <div class="space-y-2">
                <p><strong>Descripción:</strong> {{ $descripcion }}</p>
                <p><strong>Tipo:</strong> {{ $tipo }}</p>
                <p><strong>Marca:</strong> {{ $marca }}</p>
                <p><strong>Observación:</strong> {{ $observacion }}</p>
                <p><strong>Placa:</strong> {{ $placa }}</p>
                <p><strong>Año:</strong> {{ $ano }}</p>
                <p><strong>Color:</strong> {{ $color }}</p>
                <p><strong>Batería:</strong> {{ $bateria }}</p>
                <p><strong>Ubicación:</strong> {{ $ubicacion }}</p>
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
        <div class="rounded shadow-lg p-6 w-full max-w-sm z-10 bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100">
            <h2 class="text-lg font-bold mb-4">Eliminar Vehículo</h2>
            <p class="mb-4">¿Estás seguro de que deseas eliminar este vehículo? Esta acción no se puede deshacer.</p>
            <div class="flex justify-end space-x-2">
                <button wire:click="$set('modalConfirmDelete', false)" class="px-4 py-2 bg-gray-300 dark:bg-neutral-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-neutral-500">Cancelar</button>
                <button wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 dark:hover:bg-red-500">Eliminar</button>
            </div>
        </div>
    </div>
    @endif

</div>
