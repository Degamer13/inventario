<div class="p-6 bg-gray-50 dark:bg-neutral-700 min-h-screen text-gray-900 dark:text-gray-100">

    <h2 class="text-2xl font-bold mb-6">Registro de Salida de Inventario</h2>
    <hr class="mb-4 border-gray-300 dark:border-neutral-600">

    <div class="rounded shadow-lg p-6 w-full z-10 bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100">
        <form wire:submit.prevent="store">

            {{-- Mensajes de Éxito/Error --}}
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

                {{-- Entregado por --}}
                <div>
                    <label for="entregado_por_name" class="block text-sm font-medium mb-1">**Entregado por:**</label>
                    <div
                        x-data="{
                            is_open: false,
                            active_index: -1,
                            selected_name: @entangle('searchEntregado').defer,
                            selectOption(id, name) {
                                @this.set('entregado_por_id', id);
                                this.selected_name = name;
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
                                        this.selectOption(selectedItem.id, selectedItem.name + ' (' + selectedItem.cargo + ')');
                                    }
                                }
                            }
                        }"
                        @click.away="is_open = false; active_index = -1"
                        class="relative"
                    >
                        <button type="button" @click="is_open = !is_open; $nextTick(() => is_open && $refs.searchInput.focus())"
                                class="w-full text-left rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100 text-sm shadow-sm flex justify-between items-center">
                            <span x-text="selected_name || 'Seleccione de la lista...'"></span>
                            <svg class="h-5 w-5 transition-transform duration-200" :class="{'transform rotate-180': is_open}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <input type="hidden" wire:model.defer="entregado_por_id">
                        <div x-show="is_open" x-transition
                             class="absolute z-30 mt-1 w-full rounded-md bg-white dark:bg-neutral-800 shadow-lg border border-gray-200 dark:border-neutral-700 max-h-60 overflow-y-auto">
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
                                            @click="selectOption({{ $r->id }}, '{{ $r->name }} ({{ $r->cargo }})')"
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

                {{-- Recibido por (igual estructura) --}}
                <div>
                    <label for="recibido_por_name" class="block text-sm font-medium mb-1">**Recibido por:**</label>
                    <div x-data="{
                        is_open: false,
                        active_index: -1,
                        selected_name: @entangle('searchRecibido').defer,
                        selectOption(id, name) {
                            @this.set('recibido_por_id', id);
                            this.selected_name = name;
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
                                    this.selectOption(selectedItem.id, selectedItem.name + ' (' + selectedItem.cargo + ')');
                                }
                            }
                        }
                    }" @click.away="is_open = false; active_index = -1" class="relative">
                        <button type="button" @click="is_open = !is_open; $nextTick(() => is_open && $refs.searchInput.focus())"
                                class="w-full text-left rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100 text-sm shadow-sm flex justify-between items-center">
                            <span x-text="selected_name || 'Seleccione de la lista...'"></span>
                            <svg class="h-5 w-5 transition-transform duration-200" :class="{'transform rotate-180': is_open}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <input type="hidden" wire:model.defer="recibido_por_id">
                        <div x-show="is_open" x-transition class="absolute z-30 mt-1 w-full rounded-md bg-white dark:bg-neutral-800 shadow-lg border border-gray-200 dark:border-neutral-700 max-h-60 overflow-y-auto">
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
                                            @click="selectOption({{ $r->id }}, '{{ $r->name }} ({{ $r->cargo }})')"
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
            <button type="button" wire:click="addDetalle()" class="mb-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-sm font-semibold">
                + Agregar Ítem
            </button>

            {{-- Observaciones --}}
            <div class="mb-6">
                <label for="observaciones" class="block text-sm font-medium mb-1">Observaciones:</label>
                <textarea id="observaciones" wire:model.defer="observaciones" rows="3" class="w-full rounded border border-gray-300 dark:border-neutral-700 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100"></textarea>
            </div>

            {{-- Botón Guardar --}}
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 font-semibold">
                    Guardar
                </button>
            </div>

        </form>
    </div>
</div>
