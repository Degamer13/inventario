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
    <div class="p-6 bg-white dark:bg-neutral-700 rounded shadow flex flex-col items-center justify-center">
        <flux:icon name="user-group" class="h-8 w-8 text-yellow-500 mb-2" />
        <span class="text-3xl font-bold">{{ $responsablesCount }}</span>
        <span class="mt-2 text-gray-500 dark:text-gray-300">Responsables</span>
    </div>

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

