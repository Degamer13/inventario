<div>
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
    {{-- Usuarios --}}
    <div class="p-6 bg-white dark:bg-neutral-700 rounded shadow flex flex-col items-center justify-center">
        <flux:icon name="users" class="h-8 w-8 text-indigo-600 mb-2" />
        <span class="text-3xl font-bold">{{ $usersCount }}</span>
        <span class="mt-2 text-gray-500 dark:text-gray-300">Usuarios</span>
    </div>

    {{-- Roles --}}
    <div class="p-6 bg-white dark:bg-neutral-700 rounded shadow flex flex-col items-center justify-center">
        <flux:icon name="shield-check" class="h-8 w-8 text-green-600 mb-2" />
        <span class="text-3xl font-bold">{{ $rolesCount }}</span>
        <span class="mt-2 text-gray-500 dark:text-gray-300">Roles</span>
    </div>

    {{-- Responsables --}}
    @can('list responsable')
    <div class="p-6 bg-white dark:bg-neutral-700 rounded shadow flex flex-col items-center justify-center">
        <flux:icon name="user-group" class="h-8 w-8 text-yellow-500 mb-2" />
        <span class="text-3xl font-bold">{{ $responsablesCount }}</span>
        <span class="mt-2 text-gray-500 dark:text-gray-300">Responsables</span>
    </div>
    @endcan

    {{-- Materiales --}}
    <div class="p-6 bg-white dark:bg-neutral-700 rounded shadow flex flex-col items-center justify-center">
        <flux:icon name="cube" class="h-8 w-8 text-red-600 mb-2" />
        <span class="text-3xl font-bold">{{ $materialesCount }}</span>
        <span class="mt-2 text-gray-500 dark:text-gray-300">Materiales</span>
    </div>

    {{-- Facilidades --}}
    <div class="p-6 bg-white dark:bg-neutral-700 rounded shadow flex flex-col items-center justify-center">
        <flux:icon name="building-office" class="h-8 w-8 text-purple-600 mb-2" />
        <span class="text-3xl font-bold">{{ $facilidadesCount }}</span>
        <span class="mt-2 text-gray-500 dark:text-gray-300">Facilidades</span>
    </div>

    {{-- Maquinarias Fijas --}}
    <div class="p-6 bg-white dark:bg-neutral-700 rounded shadow flex flex-col items-center justify-center">
        <flux:icon name="cog" class="h-8 w-8 text-blue-600 mb-2" />
        <span class="text-3xl font-bold">{{ $maquinariasFijasCount }}</span>
        <span class="mt-2 text-gray-500 dark:text-gray-300">Maquinarias Fijas</span>
    </div>

    {{-- Sistemas --}}
    <div class="p-6 bg-white dark:bg-neutral-700 rounded shadow flex flex-col items-center justify-center">
        <flux:icon name="laptop" class="h-8 w-8 text-teal-600 mb-2" />
        <span class="text-3xl font-bold">{{ $sistemasCount }}</span>
        <span class="mt-2 text-gray-500 dark:text-gray-300">Sistemas</span>
    </div>

    {{-- Vehículos --}}
    <div class="p-6 bg-white dark:bg-neutral-700 rounded shadow flex flex-col items-center justify-center">
        <flux:icon name="truck" class="h-8 w-8 text-orange-500 mb-2" />
        <span class="text-3xl font-bold">{{ $vehiculosCount }}</span>
        <span class="mt-2 text-gray-500 dark:text-gray-300">Vehículos</span>
    </div>
    {{-- Salidas --}}
    <div class="p-6 bg-white dark:bg-neutral-700 rounded shadow flex flex-col items-center justify-center">
        <flux:icon name="notebook" class="h-8 w-8 text-pink-600 mb-2" />
        <span class="text-3xl font-bold">{{ $salidasCount }}</span>
        <span class="mt-2 text-gray-500 dark:text-gray-300">Registros de Salidas</span>
    </div>

    {{-- Requisiciones --}}
    <div class="p-6 bg-white dark:bg-neutral-700 rounded shadow flex flex-col items-center justify-center">
        <flux:icon name="book-open-text" class="h-8 w-8 text-cyan-600 mb-2" />
        <span class="text-3xl font-bold">{{ $requisicionesCount }}</span>
        <span class="mt-2 text-gray-500 dark:text-gray-300">Requisiciones</span>
        @if ($requisicionesPendientes > 0)
            <span class="mt-1 text-xs font-semibold text-yellow-600 dark:text-yellow-400">{{ $requisicionesPendientes }} pendiente(s)</span>
        @endif
    </div>
</div>

{{-- REQUISICIONES PENDIENTES PARA ADMIN / PLANIFICADOR / PROCURA --}}
@if ($esGestorRequisiciones)
    <div class="mb-4 rounded shadow-lg p-6 bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold flex items-center gap-2">
                <flux:icon name="book-open-text" class="h-5 w-5 text-cyan-600" />
                Requisiciones Pendientes por Atender
                @if ($requisicionesPendientes > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300">
                        {{ $requisicionesPendientes }}
                    </span>
                @endif
            </h3>
            <a href="{{ route('requisiciones.index') }}" wire:navigate
               class="px-3 py-1.5 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Ver todas
            </a>
        </div>

        @if ($pendientesRequisiciones->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700 text-sm">
                    <thead class="bg-gray-100 dark:bg-neutral-800">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium">N° Requisición</th>
                            <th class="px-3 py-2 text-left font-medium">Tipo</th>
                            <th class="px-3 py-2 text-left font-medium">Fecha</th>
                            <th class="px-3 py-2 text-left font-medium">Proyecto</th>
                            <th class="px-3 py-2 text-left font-medium">Prioridad</th>
                            <th class="px-3 py-2 text-left font-medium">Solicitante</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                        @foreach ($pendientesRequisiciones as $pendiente)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800">
                                <td class="px-3 py-2 font-semibold">{{ $pendiente->n_requisicion }}</td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-0.5 rounded text-xs font-bold uppercase {{ $pendiente->tipo === 'sarcos_porras' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300' }}">
                                        {{ $pendiente->tipo_label }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">{{ $pendiente->fecha->format('d/m/Y') }}</td>
                                <td class="px-3 py-2">{{ $pendiente->proyecto ?? '—' }}</td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-0.5 rounded text-xs font-bold uppercase
                                        {{ $pendiente->prioridad === 'urgente' ? 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' : '' }}
                                        {{ $pendiente->prioridad === 'intermedio' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300' : '' }}
                                        {{ $pendiente->prioridad === 'bajo' ? 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                                        {{ $pendiente->prioridad_label }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">{{ $pendiente->preparadoPor->name ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">No hay requisiciones pendientes. 🎉</p>
        @endif
    </div>
@endif
</div>

