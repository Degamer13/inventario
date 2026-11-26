<div class="p-6 bg-gray-50 dark:bg-neutral-700 min-h-screen text-gray-900 dark:text-gray-100">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-2 md:space-y-0">
        <div class="flex w-full md:w-1/2 space-x-2">
            <input type="text" wire:model.defer="search" wire:keydown.enter="searchRequisiciones" placeholder="Buscar N° de Requisición, Proyecto, Solicitante..."
                   class="w-full rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100" />

            <button type="button" wire:click="searchRequisiciones" class="flex items-center px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 dark:hover:bg-neutral-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16.65 16.65a7.5 7.5 0 111.414-1.414l4.35 4.35z" />
                </svg>
                Buscar
            </button>
        </div>

        @can('create requisicion')
            <button wire:click="openModal" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-150 shadow-md">
                + Nueva Requisición
            </button>
        @endcan

        <button wire:click="openInstructivo" class="px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition duration-150 shadow-md" title="Instructivo de llenado 00-PC-F-01">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            Instructivo
        </button>
    </div>

    {{-- FILTROS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
        <div>
            <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Filtrar por Estado:</label>
            <select wire:model.live="filtroEstado" class="w-full rounded border border-gray-300 dark:border-neutral-700 px-3 py-2 bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100 text-sm">
                <option value="">-- Todos --</option>
                @foreach ($estadosFiltro as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Filtrar por Tipo:</label>
            <select wire:model.live="filtroTipo" class="w-full rounded border border-gray-300 dark:border-neutral-700 px-3 py-2 bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100 text-sm">
                <option value="">-- Todos --</option>
                @foreach ($tipos as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Filtrar por Prioridad:</label>
            <select wire:model.live="filtroPrioridad" class="w-full rounded border border-gray-300 dark:border-neutral-700 px-3 py-2 bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100 text-sm">
                <option value="">-- Todas --</option>
                @foreach ($prioridades as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
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

    {{-- LISTADO DE REQUISICIONES --}}
    <div class="rounded shadow-lg p-6 w-full bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100 mb-6">
        <div class="overflow-x-auto rounded shadow bg-white dark:bg-neutral-900">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead class="bg-gray-100 dark:bg-neutral-950">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">N° Requisición</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Tipo</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Fecha</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Proyecto</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Centro Costos</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Prioridad</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Estado</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Solicitante</th>
                        <th class="px-4 py-2 text-right text-sm font-medium text-gray-600 dark:text-gray-200 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @forelse ($requisiciones as $requisicion)
                        <tr class="bg-white dark:bg-neutral-900 hover:bg-gray-50 dark:hover:bg-neutral-800">
                            <td class="px-4 py-2 font-semibold">{{ $requisicion->n_requisicion }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold uppercase {{ $requisicion->tipo === 'sarcos_porras' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300' }}">
                                    {{ $requisicion->tipo_label }}
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ $requisicion->fecha->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ $requisicion->proyecto ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $requisicion->centro_costos ?? '—' }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold uppercase
                                    {{ $requisicion->prioridad === 'urgente' ? 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' : '' }}
                                    {{ $requisicion->prioridad === 'intermedio' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300' : '' }}
                                    {{ $requisicion->prioridad === 'bajo' ? 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                                    {{ $requisicion->prioridad_label }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold uppercase
                                    {{ $requisicion->estado === 'pendiente' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300' : '' }}
                                    {{ $requisicion->estado === 'verificada' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : '' }}
                                    {{ $requisicion->estado === 'aprobada' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : '' }}
                                    {{ $requisicion->estado === 'rechazada' ? 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' : '' }}">
                                    {{ $requisicion->estado_label }}
                                </span>
                                @if (in_array($requisicion->estado, ['verificada', 'aprobada']) && $requisicion->detalles->count() > 0)
                                    @php
                                        $entregados = $requisicion->detalles->where('entregado', true)->count();
                                        $totalDetalles = $requisicion->detalles->count();
                                        $pendienteTotal = $requisicion->detalles->sum(fn ($d) => $d->cantidad_pendiente);
                                    @endphp
                                    <div class="mt-1 text-[10px] font-bold {{ $pendienteTotal <= 0 ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                                        @if ($pendienteTotal <= 0)
                                            ✔ Entregado completo ({{ $entregados }}/{{ $totalDetalles }} renglones)
                                        @else
                                            Entregado {{ $entregados }}/{{ $totalDetalles }} renglones · Pendiente {{ rtrim(rtrim(number_format($pendienteTotal, 2, ',', '.'), '0'), ',') }}
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-sm">{{ $requisicion->preparadoPor->name ?? '—' }}</td>
                            <td class="px-4 py-2 text-right">
                                <div class="flex justify-end space-x-1">
                                    @can('show requisicion')
                                        <button wire:click="openDetalleModal({{ $requisicion->id }})"
                                                class="inline-flex items-center px-2 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600"
                                                title="Ver Detalles">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    @endcan

                                    @can('pdf requisicion')
                                        <button wire:click="exportPdf({{ $requisicion->id }})"
                                                class="inline-flex items-center px-2 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700"
                                                title="Exportar PDF (Nota de Requisición)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zM6 4h7v4h4v11c0 .55-.45 1-1 1H6c-.55 0-1-.45-1-1V5c0-.55.45-1 1-1zm6 12H9v-2h3c.55 0 1-.45 1-1s-.45-1-1-1h-2v-2h2c.55 0 1-.45 1-1s-.45-1-1-1H9v-2h3c.55 0 1-.45 1-1s-.45-1-1-1H7v2.58l1.41 1.41c.2.2.3.47.3.7s-.1.5-.3.7l-1.41 1.41V16h3c.55 0 1-.45 1-1s-.45-1-1-1z" />
                                            </svg>
                                        </button>
                                    @endcan

                                    @can('edit requisicion')
                                        @if ($requisicion->estado === 'pendiente')
                                            <button wire:click="openEditModal({{ $requisicion->id }})"
                                                    class="inline-flex items-center px-2 py-1 text-sm bg-amber-500 text-white rounded hover:bg-amber-600"
                                                    title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                        @endif
                                    @endcan

                                    @can('verify requisicion')
                                        @if (in_array($requisicion->estado, ['pendiente', 'verificada']))
                                            <button wire:click="openVerificarModal({{ $requisicion->id }})"
                                                    class="inline-flex items-center px-2 py-1 text-sm bg-teal-600 text-white rounded hover:bg-teal-700"
                                                    title="Verificar contra Inventario">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                </svg>
                                            </button>
                                        @endif
                                    @endcan

                                    @can('entregar requisicion')
                                        @if (in_array($requisicion->estado, ['verificada', 'aprobada']))
                                            <button wire:click="openEntregaModal({{ $requisicion->id }})"
                                                    class="inline-flex items-center px-2 py-1 text-sm bg-emerald-600 text-white rounded hover:bg-emerald-700"
                                                    title="Registrar Entrega (qué se entregó y qué queda pendiente)">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                                </svg>
                                            </button>
                                        @endif
                                    @endcan

                                    @can('approve requisicion')
                                        @if (in_array($requisicion->estado, ['pendiente', 'verificada']))
                                            <button wire:click="aprobar({{ $requisicion->id }})"
                                                    class="inline-flex items-center px-2 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700"
                                                    title="Aprobar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                        @endif
                                    @endcan

                                    @can('reject requisicion')
                                        @if (in_array($requisicion->estado, ['pendiente', 'verificada']))
                                            <button wire:click="openRechazarModal({{ $requisicion->id }})"
                                                    class="inline-flex items-center px-2 py-1 text-sm bg-orange-600 text-white rounded hover:bg-orange-700"
                                                    title="Rechazar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        @endif
                                    @endcan

                                    @can('delete requisicion')
                                        @if (in_array($requisicion->estado, ['pendiente', 'rechazada']))
                                            <button wire:click="confirmDelete({{ $requisicion->id }})"
                                                    class="inline-flex items-center px-2 py-1 text-sm bg-red-700 text-white rounded hover:bg-red-800"
                                                    title="Eliminar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                                No se encontraron requisiciones.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $requisiciones->links() }}
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- MODAL FORMULARIO (CREAR / EDITAR REQUISICIÓN)                     --}}
    {{-- ================================================================ --}}
    @if ($modalFormVisible)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity dark:bg-neutral-900 dark:bg-opacity-75" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-neutral-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all my-8 sm:align-middle w-full max-w-6xl max-h-[85vh] relative"
                     style="padding-bottom: 72px;">

                    {{-- CABECERA --}}
                    <div class="bg-white dark:bg-neutral-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200 dark:border-neutral-700">
                        <div class="flex justify-between items-center">
                            <h3 class="text-2xl font-bold leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                                {{ $editingId ? 'Editar Requisición' : 'Requisición de Procura' }}
                            </h3>
                            <button wire:click="closeModal" type="button" class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
                                <span class="sr-only">Cerrar</span>
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="mt-3 flex justify-center">
                            <img src="{{ asset($tipo === 'sarcos_porras' ? 'logo_sarcos.jpg' : 'logo.jpg') }}"
                                 alt="Logo {{ $tipo === 'sarcos_porras' ? 'Sarcos Porras' : 'Fabweld' }}"
                                 class="h-16 object-contain">
                        </div>
                    </div>

                    {{-- CONTENIDO DEL FORMULARIO --}}
                    <div class="px-4 sm:px-6 py-4 overflow-y-auto relative z-20" style="max-height: calc(85vh - 160px);">
                        <form id="requisicionForm" wire:submit.prevent="{{ $editingId ? 'update' : 'store' }}">
                            {{-- Datos de Cabecera --}}
                            <h3 class="text-xl font-semibold mb-4 text-indigo-600 dark:text-indigo-400">Datos de Cabecera</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Tipo de Requisición: <span class="text-red-500">*</span></label>
                                    <select wire:model.live="tipo" class="w-full rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                                        @foreach ($tipos as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Fecha: <span class="text-red-500">*</span></label>
                                    <input type="date" wire:model.defer="fecha" class="w-full rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                                    @error('fecha') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Prioridad: <span class="text-red-500">*</span></label>
                                    <select wire:model.defer="prioridad" class="w-full rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                                        @foreach ($prioridades as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('prioridad') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Proyecto:</label>
                                    <input type="text" wire:model.defer="proyecto" class="w-full rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                                    @error('proyecto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Centro de Costos:</label>
                                    <input type="text" wire:model.defer="centro_costos" placeholder="Ej: 2026-AA-CC" class="w-full rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                                    @error('centro_costos') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Unidad Solicitante:</label>
                                    <input type="text" wire:model.defer="unidad_solicitante" class="w-full rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                                    @error('unidad_solicitante') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            @if ($editingNRequisicion)
                                <div class="mb-4 text-sm">
                                    <span class="font-semibold">N° de Requisición:</span>
                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-neutral-700 rounded">{{ $editingNRequisicion }}</span>
                                </div>
                            @else
                                <div class="mb-4 text-xs text-gray-500 dark:text-gray-400">
                                    * El número de requisición (RQ-CC-Z) se genera automáticamente al guardar.
                                </div>
                            @endif

                            {{-- Detalles de los Ítems --}}
                            <h3 class="text-xl font-semibold mt-6 mb-4 text-indigo-600 dark:text-indigo-400">Detalles de los Materiales, Herramientas, Equipos y/o Servicios</h3>
                            <div class="overflow-x-auto rounded shadow bg-gray-50 dark:bg-neutral-800 mb-4">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                    <thead class="bg-gray-100 dark:bg-neutral-900">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200 uppercase w-[5%]">Ítem</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200 uppercase w-[8%]">Cantidad</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200 uppercase w-[10%]">Unidad</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200 uppercase w-[32%]">Descripción</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200 uppercase w-[8%]">Cert. Calidad</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200 uppercase w-[15%]">Proveedor Sugerido</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-200 uppercase w-[15%]">Observaciones</th>
                                            <th class="px-3 py-2 w-[4%]"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                        @foreach ($detalles as $index => $detalle)
                                            <tr wire:key="detalle-req-row-{{ $index }}" class="bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700">
                                                <td class="px-3 py-2 text-sm text-center font-semibold">{{ $index + 1 }}</td>
                                                <td class="px-3 py-2">
                                                    <input type="number" step="0.01" min="0.01" wire:model.defer="detalles.{{ $index }}.cantidad"
                                                           class="text-sm p-1 w-full rounded border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800">
                                                    @error("detalles.{$index}.cantidad")
                                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                                    @enderror
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="text" wire:model.defer="detalles.{{ $index }}.unidad_medida" placeholder="UND, MTS, KG..."
                                                           class="text-sm p-1 w-full rounded border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800">
                                                </td>
                                                <td class="px-3 py-2">
                                                    <textarea rows="2" wire:model.defer="detalles.{{ $index }}.descripcion" placeholder="Tipo de material, especificación técnica, medidas, norma..."
                                                              class="text-sm p-1 w-full rounded border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800"></textarea>
                                                    @error("detalles.{$index}.descripcion")
                                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                                    @enderror
                                                </td>
                                                <td class="px-3 py-2 text-center">
                                                    <input type="checkbox" wire:model.defer="detalles.{{ $index }}.certificado_calidad" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="text" wire:model.defer="detalles.{{ $index }}.proveedor_sugerido"
                                                           class="text-sm p-1 w-full rounded border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800">
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="text" wire:model.defer="detalles.{{ $index }}.observaciones"
                                                           class="text-sm p-1 w-full rounded border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800">
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

                            <button type="button" wire:click="addDetalle()" class="mb-6 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm font-semibold">
                                + Agregar Ítem
                            </button>

                            <div class="mb-6">
                                <label class="block text-sm font-medium mb-1">Observaciones Generales:</label>
                                <textarea wire:model.defer="observaciones" rows="3" class="w-full rounded border border-gray-300 dark:border-neutral-700 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100"></textarea>
                            </div>
                        </form>
                    </div>

                    {{-- FOOTER DEL MODAL --}}
                    <div class="absolute inset-x-0 bottom-0 bg-gray-50 dark:bg-neutral-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse sm:gap-3 rounded-b-lg border-t border-gray-200 dark:border-neutral-700 z-30">
                        <button type="submit" form="requisicionForm" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 dark:hover:bg-indigo-500">
                            {{ $editingId ? 'Actualizar' : 'Guardar' }}
                        </button>
                        <button wire:click="closeModal" type="button" class="mt-3 sm:mt-0 px-4 py-2 bg-gray-300 dark:bg-neutral-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-neutral-500">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ================================================================ --}}
    {{-- MODAL DETALLE DE REQUISICIÓN                                      --}}
    {{-- ================================================================ --}}
    @if ($modalDetalleVisible && $requisicionSeleccionada)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black bg-opacity-70" wire:click="closeDetalleModal"></div>

                <div class="bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100 rounded-lg shadow-lg max-w-5xl w-full z-50 p-6 relative max-h-[85vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold">Requisición {{ $requisicionSeleccionada->n_requisicion }}</h3>
                        <button wire:click="closeDetalleModal" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="mb-3 flex justify-center">
                        <img src="{{ asset($requisicionSeleccionada->tipo === 'sarcos_porras' ? 'logo_sarcos.jpg' : 'logo.jpg') }}"
                             alt="Logo {{ $requisicionSeleccionada->tipo === 'sarcos_porras' ? 'Sarcos Porras' : 'Fabweld' }}"
                             class="h-14 object-contain">
                    </div>

                    {{-- Cabecera --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4 text-sm">
                        <div><span class="font-semibold">Tipo:</span> {{ $requisicionSeleccionada->tipo_label }}</div>
                        <div><span class="font-semibold">Fecha:</span> {{ $requisicionSeleccionada->fecha->format('d/m/Y') }}</div>
                        <div><span class="font-semibold">Prioridad:</span> {{ $requisicionSeleccionada->prioridad_label }}</div>
                        <div><span class="font-semibold">Estado:</span> {{ $requisicionSeleccionada->estado_label }}</div>
                        <div><span class="font-semibold">Proyecto:</span> {{ $requisicionSeleccionada->proyecto ?? '—' }}</div>
                        <div><span class="font-semibold">Centro de Costos:</span> {{ $requisicionSeleccionada->centro_costos ?? '—' }}</div>
                        <div><span class="font-semibold">Unidad Solicitante:</span> {{ $requisicionSeleccionada->unidad_solicitante ?? '—' }}</div>
                        <div><span class="font-semibold">Preparado por:</span> {{ $requisicionSeleccionada->preparadoPor->name ?? '—' }}</div>
                        @if ($requisicionSeleccionada->verificadoPor)
                            <div><span class="font-semibold">Verificado por:</span> {{ $requisicionSeleccionada->verificadoPor->name }}
                                @if ($requisicionSeleccionada->verificada_at) ({{ $requisicionSeleccionada->verificada_at->format('d/m/Y H:i') }}) @endif
                            </div>
                        @endif
                        @if ($requisicionSeleccionada->revisadoPor)
                            <div><span class="font-semibold">Revisado por:</span> {{ $requisicionSeleccionada->revisadoPor->name }}</div>
                        @endif
                    </div>

                    @if ($requisicionSeleccionada->motivo_rechazo)
                        <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/40 border border-red-300 dark:border-red-700 rounded text-sm">
                            <span class="font-semibold">Motivo del rechazo:</span> {{ $requisicionSeleccionada->motivo_rechazo }}
                        </div>
                    @endif

                    {{-- Detalles --}}
                    <div class="overflow-x-auto mb-4">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead class="bg-gray-100 dark:bg-neutral-800">
                                <tr>
                                    <th class="px-3 py-2 text-left text-sm font-medium">Ítem</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium">Cantidad</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium">Unidad</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium">Descripción</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium">Cert.</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium">Proveedor Sug.</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium">O/C</th>
                                    @if ($isManager)
                                        <th class="px-3 py-2 text-left text-sm font-medium">¿Está en Inventario?</th>
                                        <th class="px-3 py-2 text-left text-sm font-medium">Stock</th>
                                        <th class="px-3 py-2 text-left text-sm font-medium">Obs. Verif.</th>
                                        <th class="px-3 py-2 text-left text-sm font-medium">Entrega</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                @forelse ($requisicionSeleccionada->detalles as $detalle)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800">
                                        <td class="px-3 py-2 text-sm">{{ $detalle->item }}</td>
                                        <td class="px-3 py-2 text-sm">{{ $detalle->cantidad }}</td>
                                        <td class="px-3 py-2 text-sm">{{ $detalle->unidad_medida ?? '—' }}</td>
                                        <td class="px-3 py-2 text-sm">{{ $detalle->descripcion }}</td>
                                        <td class="px-3 py-2 text-sm text-center">{{ $detalle->certificado_calidad ? 'Sí' : 'No' }}</td>
                                        <td class="px-3 py-2 text-sm">{{ $detalle->proveedor_sugerido ?? '—' }}</td>
                                        <td class="px-3 py-2 text-sm">{{ $detalle->oc ?? '—' }}</td>
                                        @if ($isManager)
                                            <td class="px-3 py-2 text-sm">
                                                @if (is_null($detalle->existe_inventario))
                                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Sin verificar</span>
                                                @elseif ($detalle->existe_inventario)
                                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">Sí
                                                        @if ($detalle->material) — {{ $detalle->material->serial }} @endif
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">No</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-sm">
                                                @if (!is_null($detalle->stock_verificado))
                                                    {{ $detalle->stock_verificado }}
                                                    @if ($detalle->material) {{ $detalle->material->unidad_medida }} @endif
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-sm">{{ $detalle->observacion_verificacion ?? '—' }}</td>
                                            <td class="px-3 py-2 text-sm">
                                                @if ($detalle->entregado)
                                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                                        Entregado {{ $detalle->cantidad_entregada }}/{{ $detalle->cantidad }} {{ $detalle->unidad_medida ?? '' }}
                                                        @if ($detalle->entregado_at) ({{ $detalle->entregado_at->format('d/m/Y H:i') }}) @endif
                                                    </span>
                                                @elseif (($detalle->cantidad_entregada ?? 0) > 0)
                                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                                        Parcial: entregado {{ $detalle->cantidad_entregada }} · pendiente {{ $detalle->cantidad_pendiente }} {{ $detalle->unidad_medida ?? '' }}
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300">
                                                        Pendiente {{ $detalle->cantidad }} {{ $detalle->unidad_medida ?? '' }}
                                                    </span>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $isManager ? 11 : 7 }}" class="px-3 py-2 text-center text-gray-400">No hay renglones en esta requisición.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($requisicionSeleccionada->observaciones)
                        <div class="text-sm mb-4">
                            <span class="font-semibold">Observaciones:</span> {{ $requisicionSeleccionada->observaciones }}
                        </div>
                    @endif

                    <div class="mt-4 text-right">
                        <button wire:click="closeDetalleModal" class="px-4 py-2 bg-gray-700 text-gray-100 rounded hover:bg-gray-600">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ================================================================ --}}
    {{-- MODAL VERIFICACIÓN CONTRA INVENTARIO (ADMINISTRADOR)              --}}
    {{-- ================================================================ --}}
    @if ($modalVerificarVisible)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black bg-opacity-70" wire:click="closeVerificarModal"></div>

                <div class="bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100 rounded-lg shadow-lg max-w-5xl w-full z-50 p-6 relative max-h-[85vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold">Verificación contra Inventario</h3>
                        <button wire:click="closeVerificarModal" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <p class="text-sm mb-4 text-gray-600 dark:text-gray-300">
                        Marque para cada renglón si el ítem <strong>existe en el inventario del sistema</strong> o no.
                        Si existe, puede vincularlo a un material del inventario para ver el stock disponible.
                    </p>

                    @foreach ($verificacion as $index => $fila)
                        <div wire:key="verif-row-{{ $index }}" class="border border-gray-200 dark:border-neutral-700 rounded p-4 mb-3">
                            <div class="text-sm font-semibold mb-2">
                                Ítem {{ $index + 1 }} — {{ $fila['descripcion'] }}
                                <span class="text-gray-500 dark:text-gray-400 font-normal">
                                    (Solicitado: {{ $fila['cantidad'] }} {{ $fila['unidad_medida'] ?? '' }})
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="checkbox" wire:model.live="verificacion.{{ $index }}.existe"
                                               class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                        Existe en el inventario del sistema
                                    </label>

                                    @if (!empty($sugerencias[$index]))
                                        <div class="mt-2">
                                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Posibles coincidencias en el inventario:</span>
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach ($sugerencias[$index] as $sugerencia)
                                                    <button type="button"
                                                            wire:click="vincularMaterial({{ $index }}, {{ $sugerencia['id'] }})"
                                                            class="text-xs px-2 py-1 rounded border border-teal-300 dark:border-teal-700 bg-teal-50 dark:bg-teal-900/30 text-teal-800 dark:text-teal-200 hover:bg-teal-100 dark:hover:bg-teal-900/60">
                                                        {{ $sugerencia['serial'] }} — {{ $sugerencia['descripcion'] }} ({{ $sugerencia['marca'] ?? 's/m' }}) [Stock: {{ $sugerencia['cantidad'] }}]
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-xs font-medium mb-1">Vincular a material del inventario:</label>
                                    <div class="flex items-center gap-2">
                                        <select wire:model.live="verificacion.{{ $index }}.material_id"
                                                class="flex-1 rounded border border-gray-300 dark:border-neutral-700 px-2 py-1.5 bg-white dark:bg-neutral-800 text-sm">
                                            <option value="">— Sin vincular —</option>
                                            @foreach ($materialesOptions as $material)
                                                <option value="{{ $material->id }}">
                                                    {{ $material->serial }} — {{ $material->descripcion }} ({{ $material->marca ?? 's/m' }}) [Stock: {{ $material->cantidad }} {{ $material->unidad_medida ?? '' }}]
                                                </option>
                                            @endforeach
                                        </select>
                                        @if (!empty($fila['material_id']))
                                            <button type="button" wire:click="desvincularMaterial({{ $index }})"
                                                    class="text-red-500 hover:text-red-700 text-sm font-bold" title="Desvincular">&times;</button>
                                        @endif
                                    </div>

                                    @if (!is_null($fila['stock_actual']))
                                        <div class="mt-1 text-xs">
                                            <span class="font-semibold">Stock disponible en el sistema:</span>
                                            <span class="px-1.5 py-0.5 rounded font-bold {{ $fila['stock_actual'] > 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' }}">
                                                {{ $fila['stock_actual'] }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="mt-2">
                                        <label class="block text-xs font-medium mb-1">O/C (Orden de Compra):</label>
                                        <input type="text" wire:model.defer="verificacion.{{ $index }}.oc"
                                               placeholder="N° de O/C (lo llena el Analista/Coordinador de Procura)"
                                               class="w-full rounded border border-gray-300 dark:border-neutral-700 px-2 py-1.5 bg-white dark:bg-neutral-800 text-sm">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2">
                                <label class="block text-xs font-medium mb-1">Observaciones de lo que HAY y lo que NO HAY en inventario:</label>
                                <textarea wire:model.defer="verificacion.{{ $index }}.observacion_verificacion" rows="2"
                                          placeholder="Ej: HAY en inventario 3 UND en almacén central. / NO HAY — se debe comprar."
                                          class="w-full rounded border border-gray-300 dark:border-neutral-700 px-2 py-1.5 bg-white dark:bg-neutral-800 text-sm"></textarea>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-4 flex justify-end space-x-2">
                        <button wire:click="closeVerificarModal" class="px-4 py-2 bg-gray-300 dark:bg-neutral-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-neutral-500">
                            Cancelar
                        </button>
                        <button wire:click="saveVerificacion" class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">
                            Guardar Verificación
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ================================================================ --}}
    {{-- MODAL REGISTRO DE ENTREGA (ADMINISTRADOR / PROCURA)              --}}
    {{-- ================================================================ --}}
    @if ($modalEntregaVisible)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black bg-opacity-70" wire:click="closeEntregaModal"></div>

                <div class="bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100 rounded-lg shadow-lg max-w-4xl w-full z-50 p-6 relative max-h-[85vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold">Registro de Entrega — Qué se entregó y qué queda pendiente</h3>
                        <button wire:click="closeEntregaModal" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <p class="text-sm mb-4 text-gray-600 dark:text-gray-300">
                        Anote la <strong>CANTIDAD ENTREGADA</strong> de cada renglón. El sistema calcula
                        automáticamente lo que queda <strong>PENDIENTE</strong>.
                    </p>

                    <div class="overflow-x-auto rounded border border-gray-200 dark:border-neutral-700 mb-4">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700 text-sm">
                            <thead class="bg-gray-100 dark:bg-neutral-800">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium w-12">Ítem</th>
                                    <th class="px-3 py-2 text-left font-medium">Descripción</th>
                                    <th class="px-3 py-2 text-left font-medium w-28">Solicitado</th>
                                    <th class="px-3 py-2 text-left font-medium w-36">Cantidad Entregada</th>
                                    <th class="px-3 py-2 text-left font-medium w-36">Pendiente</th>
                                    <th class="px-3 py-2 text-left font-medium w-36">Fecha entrega</th>
                                    <th class="px-3 py-2 text-center font-medium w-28">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700 bg-white dark:bg-neutral-900">
                                @foreach ($entregaItems as $index => $fila)
                                    <tr wire:key="entrega-row-{{ $index }}" class="hover:bg-gray-50 dark:hover:bg-neutral-800">
                                        <td class="px-3 py-2 font-semibold">{{ $fila['item'] }}</td>
                                        <td class="px-3 py-2">{{ $fila['descripcion'] }}</td>
                                        <td class="px-3 py-2">{{ $fila['cantidad'] }} {{ $fila['unidad_medida'] ?? '' }}</td>
                                        <td class="px-3 py-2">
                                            <input type="number" step="0.01" min="0" max="{{ $fila['cantidad'] }}"
                                                   wire:model.live="entregaItems.{{ $index }}.cantidad_entregada"
                                                   class="w-full rounded border border-gray-300 dark:border-neutral-700 px-2 py-1 bg-white dark:bg-neutral-800 text-sm">
                                        </td>
                                        <td class="px-3 py-2 font-bold {{ $fila['pendiente'] > 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400' }}">
                                            {{ $fila['pendiente'] }} {{ $fila['unidad_medida'] ?? '' }}
                                        </td>
                                        <td class="px-3 py-2">{{ $fila['fecha'] ?? '—' }}</td>
                                        <td class="px-3 py-2 text-center">
                                            @if ($fila['entregado'])
                                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">Completo</span>
                                            @elseif (($fila['cantidad_entregada'] ?? 0) > 0)
                                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">Parcial</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300">Pendiente</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between items-center text-sm mb-4">
                        @php
                            $entregadosCount = collect($entregaItems)->where('entregado', true)->count();
                            $totalEntrega = count($entregaItems);
                            $pendienteTotal = collect($entregaItems)->sum(function ($f) { return (float) ($f['pendiente'] ?? 0); });
                        @endphp
                        <span class="font-semibold {{ $pendienteTotal <= 0 && $totalEntrega > 0 ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                            Renglones completos: {{ $entregadosCount }} de {{ $totalEntrega }} · Total pendiente: {{ rtrim(rtrim(number_format($pendienteTotal, 2, ',', '.'), '0'), ',') }}
                        </span>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button wire:click="closeEntregaModal" class="px-4 py-2 bg-gray-300 dark:bg-neutral-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-neutral-500">
                            Cancelar
                        </button>
                        <button wire:click="saveEntrega" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">
                            Guardar Entrega
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL RECHAZAR REQUISICIÓN --}}
    @if ($modalRechazoVisible)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black bg-opacity-70" wire:click="closeRechazarModal"></div>

                <div class="bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100 rounded-lg shadow-lg max-w-lg w-full z-50 p-6 relative">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold">Rechazar Requisición</h3>
                        <button wire:click="closeRechazarModal" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Motivo del rechazo: <span class="text-red-500">*</span></label>
                        <textarea wire:model.defer="motivoRechazo" rows="4"
                                  placeholder="Explique por qué se rechaza esta requisición..."
                                  class="w-full rounded border border-gray-300 dark:border-neutral-700 px-3 py-2 bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100"></textarea>
                        @error('motivoRechazo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-4 flex justify-end space-x-2">
                        <button wire:click="closeRechazarModal" class="px-4 py-2 bg-gray-300 dark:bg-neutral-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-neutral-500">
                            Cancelar
                        </button>
                        <button wire:click="rechazar" class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">
                            Rechazar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL CONFIRMAR ELIMINACIÓN --}}
    @if ($modalConfirmDelete)
        <div class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70"></div>
            <div class="rounded shadow-lg p-6 w-full max-w-sm z-10 bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100">
                <h2 class="text-lg font-bold mb-4">Eliminar Requisición</h2>
                <p class="mb-4">¿Estás seguro de que deseas eliminar esta requisición? Esta acción no se puede deshacer.</p>
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

    {{-- ================================================================ --}}
    {{-- MODAL INSTRUCTIVO DE LLENADO (00-PC-F-01)                         --}}
    {{-- ================================================================ --}}
    @if ($modalInstructivoVisible)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black bg-opacity-70" wire:click="closeInstructivo"></div>

                <div class="bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100 rounded-lg shadow-lg max-w-4xl w-full z-50 p-6 relative max-h-[85vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold">Instructivo de Llenado — Requisición de Procura (00-PC-F-01)</h3>
                        <button wire:click="closeInstructivo" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="mb-4 text-sm">
                        <p class="font-semibold mb-1">1. OBJETIVO</p>
                        <p class="text-gray-600 dark:text-gray-300 mb-3">Gestionar las solicitudes de compra de materiales, herramientas, equipos y/o servicios para apoyar el desarrollo de los diferentes procesos de Global Supply.</p>
                        <p class="font-semibold mb-1">2. RESPONSABLE</p>
                        <p class="text-gray-600 dark:text-gray-300">Coordinador de Procura</p>
                    </div>

                    <h4 class="font-semibold mb-2 text-sm">3. INSTRUCCIONES DE LLENADO</h4>
                    <div class="overflow-x-auto rounded border border-gray-200 dark:border-neutral-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700 text-sm">
                            <thead class="bg-gray-100 dark:bg-neutral-800">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold w-1/4">CAMPO</th>
                                    <th class="px-3 py-2 text-left font-semibold">DESCRIPCIÓN</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700 bg-white dark:bg-neutral-900">
                                <tr><td class="px-3 py-2 font-medium">PROYECTO</td><td class="px-3 py-2">Anotar el nombre del proyecto asociado a la solicitud.</td></tr>
                                <tr><td class="px-3 py-2 font-medium">FECHA</td><td class="px-3 py-2">Anotar la fecha de la solicitud.</td></tr>
                                <tr><td class="px-3 py-2 font-medium">CENTRO DE COSTOS</td><td class="px-3 py-2">Indicar el centro de costos del proyecto (EJ: YYYY-AA-CC).</td></tr>
                                <tr><td class="px-3 py-2 font-medium">UNIDAD SOLICITANTE</td><td class="px-3 py-2">Indicar el nombre del proceso o unidad solicitante.</td></tr>
                                <tr><td class="px-3 py-2 font-medium">NÚMERO DE LA REQUISICIÓN</td><td class="px-3 py-2">Anotar el número de control de la requisición. Consiste en el código del centro de costos del proyecto precedido de las iniciales RQ y finalizando con un correlativo numérico (Z) de tres cifras (EJ: RQ-YYYY-AA-CC-Z).</td></tr>
                                <tr><td class="px-3 py-2 font-medium">ITEM</td><td class="px-3 py-2">Anotar el número del ítem correspondiente al renglón a solicitar.</td></tr>
                                <tr><td class="px-3 py-2 font-medium">CANTIDAD</td><td class="px-3 py-2">Anotar la cantidad correspondiente al renglón a solicitar.</td></tr>
                                <tr><td class="px-3 py-2 font-medium">UNIDAD DE MEDIDA</td><td class="px-3 py-2">Indicar la unidad de medida correspondiente al renglón a solicitar.</td></tr>
                                <tr><td class="px-3 py-2 font-medium">DESCRIPCIÓN DE LOS MATERIALES, HERRAMIENTAS, EQUIPOS Y/O SERVICIOS</td><td class="px-3 py-2">Indicar detalladamente la descripción del material o servicio solicitado en cada ítem (tipo de material, especificación técnica, medidas, norma por la cual se rige). En caso de requerir servicios, adicionalmente debe indicar (fecha de inicio y duración).</td></tr>
                                <tr><td class="px-3 py-2 font-medium">CERTIFICADO DE CALIDAD</td><td class="px-3 py-2">Indicar si requiere certificado de calidad o no.</td></tr>
                                <tr><td class="px-3 py-2 font-medium">PROVEEDOR SUGERIDO</td><td class="px-3 py-2">Indicar si tiene un proveedor sugerido para la solicitud realizada.</td></tr>
                                <tr><td class="px-3 py-2 font-medium">O/C</td><td class="px-3 py-2">Anotar el número de la orden de compra correspondiente (Para ser llenado por el Analista/Coordinador de Procura).</td></tr>
                                <tr><td class="px-3 py-2 font-medium">OBSERVACIONES</td><td class="px-3 py-2">Anotar las observaciones pertinentes a la solicitud o cualquier información adicional en caso de que aplique.</td></tr>
                                <tr><td class="px-3 py-2 font-medium">PREPARADO POR</td><td class="px-3 py-2">Indicar el nombre y firma de la persona que realiza la solicitud, así como la fecha correspondiente (Debe ser llenada por el personal responsable o encargado del producto y/o servicio o proyecto).</td></tr>
                                <tr><td class="px-3 py-2 font-medium">REVISADO POR</td><td class="px-3 py-2">Indicar el nombre y firma de la persona que revisa la solicitud, así como la fecha correspondiente (Debe ser llenada por el líder o responsable de Planificación).</td></tr>
                                <tr><td class="px-3 py-2 font-medium">APROBADO POR</td><td class="px-3 py-2">Indicar el nombre y firma de la persona que aprueba la solicitud, así como la fecha correspondiente (Debe ser llenada por el líder del centro de costos del proyecto).</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-right">
                        <button wire:click="closeInstructivo" class="px-4 py-2 bg-gray-700 text-gray-100 rounded hover:bg-gray-600">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
