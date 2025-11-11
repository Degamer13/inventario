<div class="p-6 bg-gray-50 dark:bg-neutral-700 min-h-screen text-gray-900 dark:text-gray-100">

 <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-2 md:space-y-0">
        <div class="flex w-full md:w-1/2 space-x-2">
        <input type="text" wire:model.defer="search" placeholder="Buscar N° de Control, Proyecto, Origen o Destino..."
               class="w-full rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100"
            />

         <button type="button" wire:click="searchSalidas" class="flex items-center px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 dark:hover:bg-neutral-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16.65 16.65a7.5 7.5 0 111.414-1.414l4.35 4.35z" />
                </svg>
                Buscar
            </button>
    </div>

        @can('create salida')



        <button wire:click="openModal" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-150  shadow-md">
            + Nuevo Registro
        </button>
         @endcan
</div>



    {{-- Mensajes de Éxito/Error Globales (Mantenido) --}}
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

    {{-- LISTADO DE REGISTROS (Mantenido) --}}
    <div class="rounded shadow-lg p-6 w-full bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100 mb-6">


        {{-- Tabla de Salidas --}}
       <div class="overflow-x-auto rounded shadow bg-white dark:bg-neutral-900">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
        <thead class="bg-gray-100 dark:bg-neutral-950">
            <tr>
                <th  class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">
                    N° Control

                </th>
                 <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">
                    Año
                </th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">
                    Fecha
                </th>
                <th  class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">
                    Proyecto

                </th>

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
                    <td class="px-4 py-2"> {{ $salida->destino }}</td>
                    <td class="px-4 py-2 text-sm">
                        <span class="font-semibold">{{ $salida->entregadoPor->name ?? 'N/A' }}  ({{ $salida->entregadoPor->cargo ?? 'N/A' }})
                    </td>
                     <td class="px-4 py-2 text-sm">

                        <span class="font-semibold">{{ $salida->recibidoPor->name ?? 'N/A' }}  ({{ $salida->recibidoPor->cargo ?? 'N/A' }}
                    )</td>
                    <td class="px-4 py-2 text-sm">{{ $salida->observaciones }}</td>
                    <td class="px-4 py-2 text-right">
                        <div class="flex justify-end space-x-1">
                             {{-- Ver Detalles --}}
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

                   @can('edit salida')


                            {{-- Editar --}}
                            <button wire:click="edit({{ $salida->id }})"
                                    class="inline-flex items-center px-2 py-1 text-sm bg-yellow-500 text-white rounded hover:bg-yellow-600 dark:hover:bg-yellow-400"
                                    title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z" />
                                </svg>
                            </button>

                    @endcan
                            {{-- Eliminar --}}
                          @can('delete salida')
    <button wire:click="confirmDelete({{ $salida->id }})"
        class="inline-flex items-center px-2 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700 dark:hover:bg-red-500"
        title="Eliminar">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
@endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
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


    {{-- MODAL DE REGISTRO (CON POSICIONAMIENTO ABSOLUTO PARA EL FOOTER FIJO) --}}
    @if ($modalFormVisible)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                {{-- Fondo Oscuro --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity dark:bg-neutral-900 dark:bg-opacity-75" aria-hidden="true" wire:click="closeModal"></div>

                {{-- Contenido del Modal --}}
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Contenedor principal: **Clase 'relative' añadida para posicionar el footer** --}}
                <div x-data="{ headerHeight: 0, footerHeight: 0 }"
                    x-init="
                        headerHeight = $refs.header.offsetHeight;
                        footerHeight = $refs.footer.offsetHeight;
                    "
                    class="inline-block align-bottom bg-white dark:bg-neutral-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all my-8 sm:align-middle w-full max-w-6xl max-h-[85vh] relative"
                    style="padding-bottom: 72px;" {{-- Ajuste de padding para el footer (aprox 72px) --}}>

                    {{-- CABECERA (FIJA) --}}
                    <div x-ref="header" class="bg-white dark:bg-neutral-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200 dark:border-neutral-700">
                        <div class="flex justify-between items-center">
                            <h3 class="text-2xl font-bold leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                                Registro de Salida de Inventario
                            </h3>
                            <button wire:click="closeModal" type="button" class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
                                <span class="sr-only">Cerrar</span>
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- CONTENEDOR DE SCROLL PARA EL FORMULARIO (Se elimina el flexbox y se usa un calculo de altura simple) --}}
                    <div class="px-4 sm:px-6 py-4 overflow-y-auto relative z-20"
                        :style="'max-height: calc(85vh - ' + (headerHeight + footerHeight + 20) + 'px);'">

                        {{-- INICIO DEL FORMULARIO --}}
                        <form id="salidaForm" wire:submit.prevent="store">

                            {{-- Datos de Cabecera (Mantenido) --}}
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

                            {{-- Responsables y Ubicaciones (Mantenido) --}}
                            <h3 class="text-xl font-semibold mt-6 mb-4 text-indigo-600 dark:text-indigo-400">Responsables y Ubicaciones</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                                {{-- Entregado por (Autocomplete) --}}
                                <div>
                                    <label for="entregado_por_name" class="block text-sm font-medium mb-1">**Entregado por:**</label>
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
                                            <span x-text="selected_name"></span>
                                            <svg class="h-5 w-5 transition-transform duration-200" :class="{'transform rotate-180': is_open}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <input type="hidden" wire:model.defer="entregado_por_id">
                                        <div x-show="is_open" x-transition
                                            class="absolute mt-1 w-full rounded-md bg-white dark:bg-neutral-800 shadow-lg border border-gray-200 dark:border-neutral-700 max-h-60 overflow-y-auto">
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
                                {{-- Recibido por (Autocomplete) --}}
                                <div>
                                    <label for="recibido_por_name" class="block text-sm font-medium mb-1">**Recibido por:**</label>
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
                                            <span x-text="selected_name"></span>
                                            <svg class="h-5 w-5 transition-transform duration-200" :class="{'transform rotate-180': is_open}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <input type="hidden" wire:model.defer="recibido_por_id">
                                        <div x-show="is_open" x-transition class="absolute mt-1 w-full rounded-md bg-white dark:bg-neutral-800 shadow-lg border border-gray-200 dark:border-neutral-700 max-h-60 overflow-y-auto">
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
                                {{-- Origen y Destino (Mantenido) --}}
                                <div>
                                    <label for="origen" class="block text-sm font-medium mb-1">Origen:</label>
                                    <input type="text" id="origen" wire:model.defer="origen" class="w-full rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                                </div>
                                <div>
                                    <label for="destino" class="block text-sm font-medium mb-1">Destino:</label>
                                    <input type="text" id="destino" wire:model.defer="destino" class="w-full rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                                </div>
                            </div>

                            {{-- Detalles de los Ítems (Mantenido) --}}
                            <h3 class="text-xl font-semibold mt-6 mb-4 text-indigo-600 dark:text-indigo-400">Detalles de los Ítems</h3>
                            <div class="overflow-x-auto rounded shadow bg-gray-50 dark:bg-neutral-800 mb-4">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                    <thead class="bg-gray-100 dark:bg-neutral-900">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200 uppercase w-[15%]">Tipo</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200 uppercase w-[20%]">Serial / Placa</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200 uppercase w-[40%]">Descripción</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200 uppercase w-[10%]">Cant.</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200 uppercase w-[10%]">Unidad</th>
                                            <th class="px-3 py-2 w-[5%]"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                        @foreach ($detalles as $index => $detalle)
                                            <tr wire:key="detalle-{{ $index }}" class="bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700">
                                                <td class="px-3 py-2">
                                                    <select wire:model.defer="detalles.{{ $index }}.item_tipo" class="text-sm p-1 w-full rounded border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800">
                                                        @foreach ($itemTypes as $type)
                                                            <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error("detalles.{$index}.item_tipo")
                                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                                    @enderror
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="text" wire:model.defer="detalles.{{ $index }}.item_serial_placa" class="text-sm p-1 w-full rounded border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800">
                                                    @error("detalles.{$index}.item_serial_placa")
                                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                                    @enderror
                                                    @if(isset($detalles[$index]['error']) && $detalles[$index]['error'])
                                                         <p class="text-red-600 text-xs mt-1 font-bold">{{ $detalles[$index]['error'] }}</p>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="text" wire:model.defer="detalles.{{ $index }}.descripcion" class="text-sm p-1 w-full rounded border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800">
                                                    @error("detalles.{$index}.descripcion")
                                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                                    @enderror
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="number"
                                                            wire:model.defer="detalles.{{ $index }}.cantidad_salida"
                                                            min="1"
                                                            class="text-sm p-1 w-full rounded border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800"
                                                            @if($detalle['item_tipo'] === 'vehiculo') readonly @endif>
                                                    @error("detalles.{$index}.cantidad_salida")
                                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                                    @enderror
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="text"
                                                            wire:model.defer="detalles.{{ $index }}.unidad_medida"
                                                            class="text-sm p-1 w-full rounded border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800"
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

                            {{-- Botón Agregar Ítem --}}
                            <button type="button" wire:click="addDetalle()" class="mb-6 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-sm font-semibold">
                                + Agregar Ítem
                            </button>

                            {{-- Observaciones --}}
                            <div class="mb-6">
                                <label for="observaciones" class="block text-sm font-medium mb-1">Observaciones:</label>
                                <textarea id="observaciones" wire:model.defer="observaciones" rows="3" class="w-full rounded border border-gray-300 dark:border-neutral-700 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100"></textarea>
                            </div>

                        </form>
                        {{-- FIN DEL FORMULARIO --}}

                    </div>

                    {{-- FOOTER DEL MODAL (**Posición Absoluta para forzar la visibilidad**) --}}
                    <div x-ref="footer" class="absolute inset-x-0 bottom-0 bg-gray-50 dark:bg-neutral-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg border-t border-gray-200 dark:border-neutral-700 z-30">
                        <button type="submit" form="salidaForm" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Guardar Registro
                        </button>
                        <button wire:click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm dark:bg-neutral-700 dark:text-gray-100 dark:border-neutral-600 dark:hover:bg-neutral-600">
                            Cancelar
                        </button>
                    </div>

                </div>

            </div>
        </div>

    @endif
</div>


@if ($modalDetalleVisible)
<div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        {{-- Fondo oscuro semi-transparente --}}
        <div class="fixed inset-0 bg-black bg-opacity-70" wire:click="closeDetalleModal"></div>

        {{-- Contenido del modal --}}
        <div class="bg-neutral-900 text-gray-100 rounded-lg shadow-lg max-w-4xl w-full z-50 p-6 relative">
            <h3 class="text-xl font-bold mb-4">Detalles de la Salida </h3>

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
{{-- Modal Confirmar Eliminación --}}
@if($modalConfirmDelete)
<div class="fixed inset-0 flex items-center justify-center z-50">
    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70"></div>
    <div
        class="rounded shadow-lg p-6 w-full max-w-sm z-10 bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100">
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
