<div class="p-6 bg-gray-50 dark:bg-neutral-700 min-h-screen text-gray-900 dark:text-gray-100">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-2 md:space-y-0">
        <div class="flex w-full md:w-1/2 space-x-2">
            <input type="text" wire:model.defer="search" placeholder="Buscar N° de Control, Proyecto, Origen o Destino..."
                   class="w-full rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100" />

            <button type="button" wire:click="searchSalidas" class="flex items-center px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 dark:hover:bg-neutral-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16.65 16.65a7.5 7.5 0 111.414-1.414l4.35 4.35z" />
                </svg>
                Buscar
            </button>
        </div>

        @can('create salida')
            <button wire:click="openModal" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-150 shadow-md">
                + Nuevo Registro
            </button>
        @endcan
    </div>

    {{-- Mensajes de Éxito/Error Globales --}}
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 dark:bg-green-900 dark:border-green-700 dark:text-green-300" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 dark:bg-red-900 dark:border-red-700 dark:text-red-300" role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- LISTADO DE REGISTROS --}}
    <div class="rounded shadow-lg p-6 w-full bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100 mb-6">
        <div class="overflow-x-auto rounded shadow bg-white dark:bg-neutral-900">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead class="bg-gray-100 dark:bg-neutral-950">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">N° Control</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Año</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Fecha</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Proyecto</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Origen</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Destino</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Entregado Por</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Recibido Por</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Observación</th>
                        <th class="px-4 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @forelse ($salidas as $salida)
                        <tr class="bg-white dark:bg-neutral-900 hover:bg-gray-50 dark:hover:bg-neutral-800">
                            <td class="px-4 py-2">{{ $salida->n_control }}</td>
                            <td class="px-4 py-2">{{ $salida->ano }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($salida->fecha)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ $salida->proyecto }}</td>
                            <td class="px-4 py-2">{{ $salida->origen }}</td>
                            <td class="px-4 py-2">{{ $salida->destino }}</td>
                            <td class="px-4 py-2 text-sm">
                                <span class="font-semibold">{{ $salida->entregadoPor->name ?? 'N/A' }} ({{ $salida->entregadoPor->cargo ?? 'N/A' }})</span>
                            </td>
                            <td class="px-4 py-2 text-sm">
                                <span class="font-semibold">{{ $salida->recibidoPor->name ?? 'N/A' }} ({{ $salida->recibidoPor->cargo ?? 'N/A' }})</span>
                            </td>
                            <td class="px-4 py-2 text-sm">{{ $salida->observaciones }}</td>
                            <td class="px-4 py-2 text-right">
                                <div class="flex justify-end space-x-1">
                                    @can('show salida')
                                        <button wire:click="openDetalleModal({{ $salida->id }})"
                                                class="inline-flex items-center px-2 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600 dark:hover:bg-blue-400"
                                                title="Ver Detalles">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    @endcan

                                    @can('pdf salida')
                                        <button wire:click="exportPdf({{ $salida->id }})"
                                            class="inline-flex items-center px-2 py-1 text-sm bg-red-600 text-white rounded-md shadow hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-400 transition-all duration-200"
                                            title="Exportar PDF">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zM6 4h7v4h4v11c0 .55-.45 1-1 1H6c-.55 0-1-.45-1-1V5c0-.55.45-1 1-1zm6 12H9v-2h3c.55 0 1-.45 1-1s-.45-1-1-1h-2v-2h2c.55 0 1-.45 1-1s-.45-1-1-1H9v-2h3c.55 0 1-.45 1-1s-.45-1-1-1H7v2.58l1.41 1.41c.2.2.3.47.3.7s-.1.5-.3.7l-1.41 1.41V16h3c.55 0 1-.45 1-1s-.45-1-1-1z" />
                                            </svg>
                                        </button>
                                    @endcan

                                    @can('delete salida')
                                        <button wire:click="confirmDelete({{ $salida->id }})"
                                            class="inline-flex items-center px-2 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700 dark:hover:bg-red-500"
                                            title="Eliminar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                                No se encontraron registros de salidas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $salidas->links() }}
        </div>
    </div>

    {{-- MODAL PRINCIPAL DE REGISTRO --}}
    @if ($modalFormVisible)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity dark:bg-neutral-900 dark:bg-opacity-75" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-data="{ headerHeight: 65, footerHeight: 70 }"
                    x-init="$nextTick(() => {
                        if ($refs.header) headerHeight = $refs.header.offsetHeight;
                        if ($refs.footer) footerHeight = $refs.footer.offsetHeight;
                    })"
                    class="inline-block align-bottom bg-white dark:bg-neutral-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all my-8 sm:align-middle w-full max-w-6xl max-h-[85vh] relative"
                    style="padding-bottom: 72px;">

                    {{-- CABECERA (FIJA) --}}
                    <div x-ref="header" class="bg-white dark:bg-neutral-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200 dark:border-neutral-700">
                        <div class="flex justify-between items-center">
                            <h3 class="text-2xl font-bold leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                                Registro de Salida de Inventario
                            </h3>
                            <button wire:click="closeModal" type="button" class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
                                <span class="sr-only">Cerrar</span>
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- CONTENEDOR DE SCROLL PARA EL FORMULARIO --}}
                    <div class="px-4 sm:px-6 py-4 overflow-y-auto relative z-20"
                        :style="'max-height: calc(85vh - ' + (headerHeight + footerHeight + 20) + 'px);'">

                        <form id="salidaForm" wire:submit.prevent="store">
                            {{-- Datos de Cabecera --}}
                            <h3 class="text-xl font-semibold mb-4 text-indigo-600 dark:text-indigo-400">Datos de Cabecera</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                                <div>
                                    <label for="n_control" class="block text-sm font-medium mb-1">N° de Control:</label>
                                    <input type="text" id="n_control" wire:model.defer="n_control" class="w-full rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                                    @error('n_control') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="fecha" class="block text-sm font-medium mb-1">Fecha:</label>
                                    <input type="date" id="fecha" wire:model.defer="fecha" class="w-full rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                                    @error('fecha') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="proyecto" class="block text-sm font-medium mb-1">Proyecto:</label>
                                    <input type="text" id="proyecto" wire:model.defer="proyecto" class="w-full rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                                    @error('proyecto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="ano" class="block text-sm font-medium mb-1">Año:</label>
                                    <input type="number" id="ano" wire:model.defer="ano" class="w-full rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                                    @error('ano') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Responsables y Ubicaciones --}}
                            <h3 class="text-xl font-semibold mt-6 mb-4 text-indigo-600 dark:text-indigo-400">Responsables y Ubicaciones</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

                                {{-- Entregado por (Autocomplete con botón +) --}}
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-sm font-medium">Entregado por:</label>
                                        <button type="button"
                                                wire:click="openQuickResponsable('entregado')"
                                                title="Crear nuevo responsable"
                                                class="inline-flex items-center px-1.5 py-0.5 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded transition shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Nuevo
                                        </button>
                                    </div>

                                    <div
                                        x-data="{
                                            is_open: false,
                                            active_index: -1,
                                            selected_name: @entangle('entregado_por_name').defer,
                                            selectOption(id, name, cargo) {
                                                @this.set('entregado_por_id', id);
                                                this.selected_name = name + ' (' + cargo + ')';
                                                @this.set('searchEntregado', '');
                                                this.is_open = false;
                                                this.active_index = -1;
                                            },
                                            navigate(direction) {
                                                if (!this.is_open) { this.is_open = true; this.active_index = 0; return; }
                                                const count = this.$wire.responsables.length;
                                                if (direction === 'down') { this.active_index = (this.active_index < count - 1) ? this.active_index + 1 : 0; }
                                                else if (direction === 'up') { this.active_index = (this.active_index > 0) ? this.active_index - 1 : count - 1; }
                                                this.$nextTick(() => {
                                                    const element = document.getElementById('entregado-item-' + this.active_index);
                                                    if (element) { element.scrollIntoView({ block: 'nearest' }); }
                                                });
                                            },
                                            selectActive() {
                                                if (this.active_index >= 0) {
                                                    const selectedItem = this.$wire.responsables[this.active_index];
                                                    if (selectedItem) {
                                                        this.selectOption(selectedItem.id, selectedItem.name, selectedItem.cargo);
                                                    }
                                                }
                                            }
                                        }"
                                        @click.away="is_open = false; active_index = -1"
                                        class="relative"
                                    >
                                        <button type="button" @click="is_open = !is_open; $nextTick(() => is_open && $refs.searchInput.focus())"
                                                class="w-full text-left rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100 text-sm shadow-sm flex justify-between items-center">
                                            <span x-text="selected_name || 'Seleccione un responsable...'"></span>
                                            <svg class="h-5 w-5 transition-transform duration-200" :class="{'transform rotate-180': is_open}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <input type="hidden" wire:model.defer="entregado_por_id">
                                        <div x-show="is_open" x-transition
                                            class="absolute mt-1 w-full rounded-md bg-white dark:bg-neutral-800 shadow-lg border border-gray-200 dark:border-neutral-700 max-h-60 overflow-y-auto z-40">
                                            <div class="p-2 border-b border-gray-200 dark:border-neutral-700 sticky top-0 bg-white dark:bg-neutral-800 z-40">
                                                <input x-ref="searchInput" type="text" wire:model.live.debounce.300ms="searchEntregado"
                                                        @keydown.escape.prevent="is_open = false"
                                                        @keydown.arrow-down.prevent="navigate('down')"
                                                        @keydown.arrow-up.prevent="navigate('up')"
                                                        @keydown.enter.prevent="selectActive()"
                                                        placeholder="Buscar responsable..."
                                                        class="w-full rounded border border-gray-300 dark:border-neutral-700 px-3 py-1 bg-white dark:bg-neutral-700 text-gray-900 dark:text-gray-100 text-sm shadow-inner"
                                                        autocomplete="off">
                                            </div>
                                            <ul class="py-1" x-ref="listbox">
                                                @if (count($this->responsables) > 0)
                                                    @foreach ($this->responsables as $index => $r)
                                                        <li id="entregado-item-{{ $index }}" wire:key="entregado-{{ $r->id }}"
                                                            @click="selectOption({{ $r->id }}, '{{ $r->name }}', '{{ $r->cargo }}')"
                                                            :class="{ 'bg-indigo-500 text-white dark:bg-indigo-600': active_index === {{ $index }}, 'text-gray-700 dark:text-gray-200': active_index !== {{ $index }} }"
                                                            class="cursor-pointer px-4 py-2 text-sm transition duration-150 ease-in-out hover:bg-indigo-500 hover:text-white dark:hover:bg-indigo-600">
                                                            {{ $r->name }} ({{ $r->cargo }})
                                                        </li>
                                                    @endforeach
                                                @else
                                                    <li class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">No se encontraron responsables.</li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                    @error('entregado_por_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- Recibido por (Autocomplete con botón +) --}}
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-sm font-medium">Recibido por:</label>
                                        <button type="button"
                                                wire:click="openQuickResponsable('recibido')"
                                                title="Crear nuevo responsable"
                                                class="inline-flex items-center px-1.5 py-0.5 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded transition shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Nuevo
                                        </button>
                                    </div>

                                    <div x-data="{
                                        is_open: false,
                                        active_index: -1,
                                        selected_name: @entangle('recibido_por_name').defer,
                                        selectOption(id, name, cargo) {
                                            @this.set('recibido_por_id', id);
                                            this.selected_name = name + ' (' + cargo + ')';
                                            @this.set('searchRecibido', '');
                                            this.is_open = false;
                                            this.active_index = -1;
                                        },
                                        navigate(direction) {
                                            if (!this.is_open) { this.is_open = true; this.active_index = 0; return; }
                                            const count = this.$wire.responsables.length;
                                            if (direction === 'down') { this.active_index = (this.active_index < count - 1) ? this.active_index + 1 : 0; }
                                            else if (direction === 'up') { this.active_index = (this.active_index > 0) ? this.active_index - 1 : count - 1; }
                                            this.$nextTick(() => {
                                                const element = document.getElementById('recibido-item-' + this.active_index);
                                                if (element) { element.scrollIntoView({ block: 'nearest' }); }
                                            });
                                        },
                                        selectActive() {
                                            if (this.active_index >= 0) {
                                                const selectedItem = this.$wire.responsables[this.active_index];
                                                if (selectedItem) {
                                                    this.selectOption(selectedItem.id, selectedItem.name, selectedItem.cargo);
                                                }
                                            }
                                        }
                                    }" @click.away="is_open = false; active_index = -1" class="relative">
                                        <button type="button" @click="is_open = !is_open; $nextTick(() => is_open && $refs.searchInput.focus())"
                                                class="w-full text-left rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100 text-sm shadow-sm flex justify-between items-center">
                                            <span x-text="selected_name || 'Seleccione un responsable...'"></span>
                                            <svg class="h-5 w-5 transition-transform duration-200" :class="{'transform rotate-180': is_open}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <input type="hidden" wire:model.defer="recibido_por_id">
                                        <div x-show="is_open" x-transition class="absolute mt-1 w-full rounded-md bg-white dark:bg-neutral-800 shadow-lg border border-gray-200 dark:border-neutral-700 max-h-60 overflow-y-auto z-40">
                                            <div class="p-2 border-b border-gray-200 dark:border-neutral-700 sticky top-0 bg-white dark:bg-neutral-800 z-40">
                                                <input x-ref="searchInput" type="text" wire:model.live.debounce.300ms="searchRecibido"
                                                        @keydown.escape.prevent="is_open = false"
                                                        @keydown.arrow-down.prevent="navigate('down')"
                                                        @keydown.arrow-up.prevent="navigate('up')"
                                                        @keydown.enter.prevent="selectActive()"
                                                        placeholder="Buscar responsable..."
                                                        class="w-full rounded border border-gray-300 dark:border-neutral-700 px-3 py-1 bg-white dark:bg-neutral-700 text-gray-900 dark:text-gray-100 text-sm shadow-inner"
                                                        autocomplete="off">
                                            </div>
                                            <ul class="py-1" x-ref="listbox">
                                                @if (count($this->responsables) > 0)
                                                    @foreach ($this->responsables as $index => $r)
                                                        <li id="recibido-item-{{ $index }}" wire:key="recibido-{{ $r->id }}"
                                                            @click="selectOption({{ $r->id }}, '{{ $r->name }}', '{{ $r->cargo }}')"
                                                            :class="{ 'bg-indigo-500 text-white dark:bg-indigo-600': active_index === {{ $index }}, 'text-gray-700 dark:text-gray-200': active_index !== {{ $index }} }"
                                                            class="cursor-pointer px-4 py-2 text-sm transition duration-150 ease-in-out hover:bg-indigo-500 hover:text-white dark:hover:bg-indigo-600">
                                                            {{ $r->name }} ({{ $r->cargo }})
                                                        </li>
                                                    @endforeach
                                                @else
                                                    <li class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">No se encontraron responsables.</li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                    @error('recibido_por_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- Origen y Destino --}}
                                <div>
                                    <label for="origen" class="block text-sm font-medium mb-1">Origen:</label>
                                    <input type="text" id="origen" wire:model.defer="origen" class="w-full rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                                </div>
                                <div>
                                    <label for="destino" class="block text-sm font-medium mb-1">Destino:</label>
                                    <input type="text" id="destino" wire:model.defer="destino" class="w-full rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                                </div>
                            </div>

                            {{-- Detalles de los Ítems --}}
                            <h3 class="text-xl font-semibold mt-6 mb-4 text-indigo-600 dark:text-indigo-400">Detalles de los Ítems</h3>
                            <div class="overflow-x-auto rounded shadow bg-gray-50 dark:bg-neutral-800 mb-4">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                    <thead class="bg-gray-100 dark:bg-neutral-900">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200 uppercase w-[15%]">Tipo</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200 uppercase w-[30%]">Serial / Placa / Descripción</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200 uppercase w-[30%]">Descripción</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200 uppercase w-[12%]">Cant.</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200 uppercase w-[8%]">Unidad</th>
                                            <th class="px-3 py-2 w-[5%]"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                        @foreach ($detalles as $index => $detalle)
                                            <tr wire:key="detalle-{{ $index }}" class="bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700">
                                                <td class="px-3 py-2">
                                                    <select wire:model.live="detalles.{{ $index }}.item_tipo"
                                                            class="text-sm p-1 w-full rounded border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800">
                                                        <option value="">-- Tipo --</option>
                                                        @foreach ($itemTypes as $type)
                                                            <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error("detalles.{$index}.item_tipo")
                                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                                    @enderror
                                                </td>

                                                {{-- SELECTOR DESPLEGABLE CON BUSCADOR (IDÉNTICO A RESPONSABLES) --}}
                                                <td class="px-3 py-2">
                                                    <div
                                                        x-data="{
                                                            is_open: false,
                                                            active_index: -1,
                                                            search_query: '',
                                                            selectItem(serial) {
                                                                @this.set('detalles.{{ $index }}.item_serial_placa', serial);
                                                                this.is_open = false;
                                                                this.active_index = -1;
                                                                this.search_query = '';
                                                            },
                                                            navigate(direction) {
                                                                const list = this.getFilteredItems();
                                                                if (!this.is_open) { this.is_open = true; this.active_index = 0; return; }
                                                                const count = list.length;
                                                                if (count === 0) return;

                                                                if (direction === 'down') {
                                                                    this.active_index = (this.active_index < count - 1) ? this.active_index + 1 : 0;
                                                                } else if (direction === 'up') {
                                                                    this.active_index = (this.active_index > 0) ? this.active_index - 1 : count - 1;
                                                                }

                                                                this.$nextTick(() => {
                                                                    const el = document.getElementById('item-opt-{{ $index }}-' + this.active_index);
                                                                    if (el) el.scrollIntoView({ block: 'nearest' });
                                                                });
                                                            },
                                                            selectActive() {
                                                                const list = this.getFilteredItems();
                                                                if (this.active_index >= 0 && list[this.active_index]) {
                                                                    this.selectItem(list[this.active_index].serial_placa);
                                                                }
                                                            },
                                                            getFilteredItems() {
                                                                const all = @js($filteredSerials[$index] ?? []);
                                                                if (!this.search_query.trim()) return all;
                                                                const q = this.search_query.toLowerCase();
                                                                return all.filter(i => (i.display_search && i.display_search.toLowerCase().includes(q)));
                                                            }
                                                        }"
                                                        @click.away="is_open = false; active_index = -1"
                                                        class="relative"
                                                    >
                                                        <button type="button"
                                                                @click="is_open = !is_open; $nextTick(() => is_open && $refs.searchInput.focus())"
                                                                class="w-full text-left rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 px-2 py-1.5 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100 text-xs shadow-sm flex justify-between items-center">
                                                            <span class="truncate">
                                                                {{ $detalle['item_serial_placa'] ? $detalle['item_serial_placa'] : '-- Seleccionar Ítem --' }}
                                                            </span>
                                                            <svg class="h-4 w-4 text-gray-400 transition-transform duration-200 flex-shrink-0 ml-1"
                                                                 :class="{'transform rotate-180': is_open}"
                                                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>

                                                        <div x-show="is_open" x-transition
                                                             style="z-index: 55;"
                                                             class="absolute mt-1 w-80 max-w-xs sm:max-w-sm rounded-md bg-white dark:bg-neutral-800 shadow-xl border border-gray-200 dark:border-neutral-700 max-h-60 overflow-y-auto left-0">
                                                            <div class="p-2 border-b border-gray-200 dark:border-neutral-700 sticky top-0 bg-white dark:bg-neutral-800 z-10">
                                                                <input x-ref="searchInput"
                                                                       type="text"
                                                                       x-model="search_query"
                                                                       @keydown.escape.prevent="is_open = false"
                                                                       @keydown.arrow-down.prevent="navigate('down')"
                                                                       @keydown.arrow-up.prevent="navigate('up')"
                                                                       @keydown.enter.prevent="selectActive()"
                                                                       placeholder="Buscar serial, placa, descripción..."
                                                                       class="w-full rounded border border-gray-300 dark:border-neutral-700 px-2.5 py-1 bg-white dark:bg-neutral-700 text-gray-900 dark:text-gray-100 text-xs shadow-inner focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                                                       autocomplete="off">
                                                            </div>
                                                            <ul class="py-1">
                                                                <template x-for="(item, idx) in getFilteredItems()" :key="idx">
                                                                    <li :id="'item-opt-{{ $index }}-' + idx"
                                                                        @click="selectItem(item.serial_placa)"
                                                                        @mouseenter="active_index = idx"
                                                                        :class="{
                                                                            'bg-indigo-600 text-white': active_index === idx,
                                                                            'text-gray-700 dark:text-gray-200': active_index !== idx
                                                                        }"
                                                                        class="cursor-pointer px-3 py-1.5 text-xs transition duration-100 ease-in-out hover:bg-indigo-600 hover:text-white"
                                                                        x-text="item.display_search">
                                                                    </li>
                                                                </template>

                                                                <template x-if="getFilteredItems().length === 0">
                                                                    <li class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400 text-center">
                                                                        No se encontraron coincidencias.
                                                                    </li>
                                                                </template>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    @error("detalles.{$index}.item_serial_placa")
                                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                                    @enderror
                                                </td>

                                                {{-- DESCRIPCIÓN --}}
                                                <td class="px-3 py-2">
                                                    <input type="text" wire:model.defer="detalles.{{ $index }}.descripcion"
                                                           class="text-sm p-1 w-full rounded border border-gray-300 dark:border-neutral-700 bg-gray-100 dark:bg-neutral-700 cursor-not-allowed"
                                                           readonly>
                                                    @error("detalles.{$index}.descripcion")
                                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                                    @enderror
                                                    @if(isset($detalle['error']) && $detalle['error'])
                                                         <p class="text-red-600 text-xs mt-1 font-bold">{{ $detalle['error'] }}</p>
                                                    @endif
                                                </td>

                                                {{-- CANTIDAD CON INDICADOR DE STOCK DISPONIBLE --}}
                                                <td class="px-3 py-2">
                                                    <input type="number"
                                                        wire:model.live.debounce.300ms="detalles.{{ $index }}.cantidad_salida"
                                                        min="1"
                                                        class="text-sm p-1 w-full rounded border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800
                                                        @if($detalle['item_tipo'] === 'vehiculo') bg-gray-100 cursor-not-allowed @endif"
                                                        @if($detalle['item_tipo'] === 'vehiculo') readonly @endif>

                                                    {{-- Muestra la cantidad disponible del ítem seleccionado --}}
                                                    @if(!is_null($detalle['stock_disponible']))
                                                        <div class="mt-1 flex items-center justify-between text-[11px] leading-tight">
                                                            <span class="text-gray-500 dark:text-gray-400">Disp:</span>
                                                            <span class="font-bold px-1.5 py-0.5 rounded {{ $detalle['stock_disponible'] > 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' }}">
                                                                {{ $detalle['stock_disponible'] }}
                                                            </span>
                                                        </div>
                                                    @endif

                                                    @error("detalles.{$index}.cantidad_salida")
                                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                                    @enderror
                                                </td>

                                                {{-- UNIDAD --}}
                                                <td class="px-3 py-2">
                                                    <input type="text"
                                                        wire:model.defer="detalles.{{ $index }}.unidad_medida"
                                                        class="text-sm p-1 w-full rounded border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800
                                                        @if($detalle['item_tipo'] === 'vehiculo') bg-gray-100 cursor-not-allowed @endif"
                                                        @if($detalle['item_tipo'] === 'vehiculo') readonly @endif>
                                                </td>

                                                <td class="px-3 py-2 text-center">
                                                    <button type="button" wire:click="removeDetalle({{ $index }})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 text-xl font-bold p-1">
                                                        &times;
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <button type="button" wire:click="addDetalle()" class="mb-6 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-sm font-semibold">
                                + Agregar Ítem
                            </button>

                            <div class="mb-6">
                                <label for="observaciones" class="block text-sm font-medium mb-1">Observaciones:</label>
                                <textarea id="observaciones" wire:model.defer="observaciones" rows="3" class="w-full rounded border border-gray-300 dark:border-neutral-700 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100"></textarea>
                            </div>
                        </form>
                    </div>

                    {{-- FOOTER DEL MODAL PRINCIPAL --}}
                    <div x-ref="footer" class="absolute inset-x-0 bottom-0 bg-gray-50 dark:bg-neutral-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse sm:gap-3 rounded-b-lg border-t border-gray-200 dark:border-neutral-700 z-30">
                        <button type="submit" form="salidaForm" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 dark:hover:bg-indigo-500">
                            Guardar
                        </button>
                        <button wire:click="closeModal" type="button" class="mt-3 sm:mt-0 px-4 py-2 bg-gray-300 dark:bg-neutral-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-neutral-500">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL DETALLES DE SALIDA --}}
    @if ($modalDetalleVisible)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black bg-opacity-70" wire:click="closeDetalleModal"></div>

                <div class="bg-neutral-900 text-gray-100 rounded-lg shadow-lg max-w-4xl w-full z-50 p-6 relative">
                    <h3 class="text-xl font-bold mb-4">Detalles de la Salida</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-neutral-800">
                                <tr>
                                    <th class="px-3 py-2 text-left text-sm font-medium">Tipo</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium">Serial / Placa</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium">Descripción</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium">Cantidad</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium">Unidad</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @forelse ($detalleSalidaItems as $detalle)
                                    <tr class="hover:bg-neutral-800">
                                        <td class="px-3 py-2">{{ $detalle->item_tipo }}</td>
                                        <td class="px-3 py-2">{{ $detalle->item_serial_placa }}</td>
                                        <td class="px-3 py-2">{{ $detalle->descripcion }}</td>
                                        <td class="px-3 py-2">{{ $detalle->cantidad_salida }}</td>
                                        <td class="px-3 py-2">{{ $detalle->unidad_medida }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-2 text-center text-gray-400">
                                            No hay detalles para esta salida.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-right">
                        <button wire:click="closeDetalleModal" class="px-4 py-2 bg-gray-700 text-gray-100 rounded hover:bg-gray-600">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL CONFIRMAR ELIMINACIÓN --}}
    @if($modalConfirmDelete)
        <div class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70"></div>
            <div class="rounded shadow-lg p-6 w-full max-w-sm z-10 bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100">
                <h2 class="text-lg font-bold mb-4">Eliminar Salida</h2>
                <p class="mb-4">¿Estás seguro de que deseas eliminar esta salida? Esta acción no se puede deshacer.</p>
                <div class="flex justify-end space-x-2">
                    <button wire:click="$set('modalConfirmDelete', false)"
                        class="px-4 py-2 bg-gray-300 dark:bg-neutral-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-neutral-500">
                        Cancelar
                    </button>
                    <button wire:click="delete"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 dark:hover:bg-red-500">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- SUB-MODAL RÁPIDO PARA RESPONSABLES (Z-INDEX 70 FORZADO) --}}
    @if($modalQuickResponsableVisible)
        <div class="fixed inset-0 overflow-y-auto" style="z-index: 70;" aria-labelledby="modal-quick-resp" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black bg-opacity-60 transition-opacity" wire:click="closeQuickResponsable"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-neutral-900 rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full relative z-20 p-6">
                    <div class="flex justify-between items-center mb-4 border-b border-gray-200 dark:border-neutral-700 pb-3">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100" id="modal-quick-resp">
                            Registrar Nuevo Responsable ({{ $responsableTarget === 'entregado' ? 'Entregado por' : 'Recibido por' }})
                        </h3>
                        <button wire:click="closeQuickResponsable" type="button" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Nombre <span class="text-red-500">*</span></label>
                            <input type="text" wire:model.defer="nuevo_resp_name" placeholder="Nombre completo" class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100 text-sm">
                            @error('nuevo_resp_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Cédula <span class="text-red-500">*</span></label>
                                <input type="text" wire:model.defer="nuevo_resp_cedula" placeholder="Cédula" class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100 text-sm">
                                @error('nuevo_resp_cedula') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Teléfono</label>
                                <input type="text" wire:model.defer="nuevo_resp_telefono" placeholder="Teléfono" class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100 text-sm">
                                @error('nuevo_resp_telefono') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
                            <input type="email" wire:model.defer="nuevo_resp_email" placeholder="correo@ejemplo.com" class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100 text-sm">
                            @error('nuevo_resp_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Cargo</label>
                                <input type="text" wire:model.defer="nuevo_resp_cargo" placeholder="Cargo" class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100 text-sm">
                                @error('nuevo_resp_cargo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Área</label>
                                <input type="text" wire:model.defer="nuevo_resp_area" placeholder="Área o Departamento" class="w-full border border-gray-300 dark:border-neutral-700 rounded px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100 text-sm">
                                @error('nuevo_resp_area') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end space-x-2 border-t border-gray-200 dark:border-neutral-700 pt-3">
                        <button type="button" wire:click="closeQuickResponsable" class="px-4 py-2 bg-gray-300 dark:bg-neutral-700 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-neutral-600 text-sm">
                            Cancelar
                        </button>
                        <button type="button" wire:click="storeQuickResponsable" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 dark:hover:bg-indigo-500 text-sm font-semibold">
                            Guardar y Asignar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
