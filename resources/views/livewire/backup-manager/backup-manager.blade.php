<div class="p-6 bg-gray-50 dark:bg-neutral-700 min-h-screen text-gray-900 dark:text-gray-100">

    {{-- Tarjeta Superior: Generar y Subir Backup --}}
    <div class="bg-white dark:bg-neutral-900 rounded shadow-sm border border-gray-200 dark:border-neutral-700 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-indigo-600 dark:text-indigo-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                    </svg>
                    Centro de Respaldos
                </h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1 font-medium">Genera, sube y administra las copias de seguridad de la base de datos y archivos.</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                {{-- Botón Nuevo: Subir Respaldo --}}
                <button
                    wire:click="$set('modalRestoreVisible', true)"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded shadow-sm transition-all duration-200 flex items-center justify-center gap-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    Subir Respaldo ZIP
                </button>

                {{-- Botón Existente: Crear Respaldo --}}
                <button
                    wire:click="generateBackup"
                    wire:loading.attr="disabled"
                    class="bg-indigo-600 hover:bg-indigo-700 dark:hover:bg-indigo-500 text-white font-bold py-2 px-6 rounded shadow-sm transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <span wire:loading.remove wire:target="generateBackup" class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Crear Respaldo Ahora
                    </span>
                    <span wire:loading wire:target="generateBackup" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Empaquetando...
                    </span>
                </button>
            </div>
        </div>

        @if($statusMessage)
            <div class="mt-4 p-4 rounded-lg flex items-center gap-3 {{ $isError ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300 border border-red-200 dark:border-red-800' : 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 border border-green-200 dark:border-green-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                <span class="font-bold text-sm">{{ $statusMessage }}</span>
                <button wire:click="$set('statusMessage', '')" class="ml-auto bg-transparent font-bold hover:opacity-75 focus:outline-none text-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        @endif
    </div>

    {{-- Buscador --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-2 md:space-y-0">
        <div class="flex w-full md:w-1/2 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16.65 16.65a7.5 7.5 0 111.414-1.414l4.35 4.35z" />
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Buscar respaldo por fecha o nombre..."
                class="w-full rounded border border-gray-300 dark:border-neutral-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-500 dark:focus:border-indigo-400 pl-10 pr-3 py-2 bg-white dark:bg-neutral-900 text-gray-900 dark:text-gray-100 shadow-sm transition-colors outline-none"
            />
        </div>
    </div>

    {{-- Tarjeta Inferior: Tabla de Historial --}}
    <div class="overflow-hidden rounded shadow bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
            <thead class="bg-gray-100 dark:bg-neutral-950 text-gray-600 dark:text-gray-200 uppercase font-bold text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Nombre del Archivo</th>
                    <th class="px-4 py-3 text-left">Fecha y Hora</th>
                    <th class="px-4 py-3 text-left">Peso</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700 text-sm font-medium">
                @forelse($backups as $backup)
                    <tr class="bg-white dark:bg-neutral-900 hover:bg-gray-50 dark:hover:bg-neutral-800 transition-colors">
                        <td class="px-4 py-3 flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 dark:text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                            {{ $backup['name'] }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-normal">{{ $backup['date'] }}</td>
                        <td class="px-4 py-3">
                            <span class="bg-gray-100 dark:bg-neutral-800 px-2 py-1 rounded border border-gray-200 dark:border-neutral-600 text-xs font-bold">
                                {{ $backup['size'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end space-x-2">
                                <button wire:click="downloadBackup('{{ $backup['path'] }}')"
                                        class="bg-blue-500 text-white p-1.5 rounded hover:bg-blue-600 transition-colors"
                                        title="Descargar">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                </button>

                                <button wire:click="confirmDelete('{{ $backup['path'] }}')"
                                        class="bg-red-600 text-white p-1.5 rounded hover:bg-red-700 transition-colors"
                                        title="Eliminar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400 font-bold">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto h-10 w-10 mb-2 opacity-50">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                            <p>No se encontraron respaldos {{ $search ? 'con ese término' : 'disponibles' }}.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Paginación estándar de Laravel --}}
        <div class="px-4 py-3 border-t border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-900">
            {{ $backups->links() }}
        </div>
    </div>

    {{-- MODAL: SUBIR RESPALDO --}}
    @if($modalRestoreVisible)
    <div class="fixed inset-0 flex items-center justify-center z-50 p-4">
        <div class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm" wire:click="$set('modalRestoreVisible', false)"></div>
        <div class="bg-white dark:bg-neutral-900 rounded-lg shadow-xl p-6 w-full max-w-md z-10 relative border border-gray-200 dark:border-neutral-700">
            <h3 class="text-xl font-bold text-emerald-600 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
                Subir Archivo de Respaldo
            </h3>

            <form wire:submit.prevent="restoreBackup">
                <div class="mb-6">
                    <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Selecciona el archivo .zip del respaldo</label>
                    <div
                        class="border-2 border-dashed border-gray-300 dark:border-neutral-700 rounded-lg p-4 text-center hover:border-emerald-500 transition-colors"
                        x-data="{ isUploading: false, progress: 0 }"
                        x-on:livewire-upload-start="isUploading = true"
                        x-on:livewire-upload-finish="isUploading = false"
                        x-on:livewire-upload-error="isUploading = false"
                        x-on:livewire-upload-progress="progress = $event.detail.progress"
                    >
                        <input type="file" wire:model="zipFile" class="hidden" id="zip_upload_input">
                        <label for="zip_upload_input" class="cursor-pointer flex flex-col items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Haz clic para seleccionar o arrastra el archivo aquí</span>
                        </label>

                        @if($zipFile)
                            <div class="mt-4 p-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 rounded text-xs font-bold border border-emerald-200">
                                Archivo cargado: {{ $zipFile->getClientOriginalName() }}
                            </div>
                        @endif

                        {{-- Barra de progreso --}}
                        <div x-show="isUploading" class="mt-4">
                            <div class="h-2 w-full bg-gray-200 dark:bg-neutral-800 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 transition-all duration-200" x-bind:style="`width: ${progress}%`"></div>
                            </div>
                            <span class="text-[10px] mt-1 block" x-text="`Subiendo: ${progress}%`"></span>
                        </div>
                    </div>
                    @error('zipFile') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="$set('modalRestoreVisible', false)" class="px-5 py-2 bg-gray-200 dark:bg-neutral-800 rounded font-bold text-sm hover:bg-gray-300 dark:hover:bg-neutral-700 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" wire:loading.attr="disabled" class="px-5 py-2 bg-emerald-600 text-white rounded font-bold text-sm hover:bg-emerald-700 transition-colors flex items-center gap-2">
                        <span wire:loading.remove wire:target="restoreBackup">Cargar en el Servidor</span>
                        <span wire:loading wire:target="restoreBackup">Guardando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal: Confirmación de Eliminación --}}
    @if($modalConfirmDelete)
    <div class="fixed inset-0 flex items-center justify-center z-50 p-4">
        <div class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm" wire:click="$set('modalConfirmDelete', false)"></div>
        <div class="bg-white dark:bg-neutral-900 rounded-lg shadow-xl p-6 w-full max-w-sm z-10 relative border border-gray-200 dark:border-neutral-700 text-center">
            <div class="flex justify-center mb-4 text-red-600">
                <div class="bg-red-100 dark:bg-red-900/30 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>

            <h2 class="text-xl font-bold text-red-600 mb-2">¿Borrar Respaldo?</h2>
            <p class="mb-6 text-gray-600 dark:text-gray-300 font-medium text-sm">
                El archivo <strong>{{ basename($backupToDelete) }}</strong> se eliminará para siempre. Esta acción no se puede deshacer.
            </p>

            <div class="flex justify-center gap-3">
                <button wire:click="$set('modalConfirmDelete', false)" class="px-5 py-2 bg-gray-200 dark:bg-neutral-800 rounded font-bold text-sm hover:bg-gray-300 dark:hover:bg-neutral-700 transition-colors">
                    Cancelar
                </button>
                <button wire:click="deleteBackup" class="px-5 py-2 bg-red-600 text-white rounded font-bold text-sm hover:bg-red-700 transition-colors">
                    Sí, eliminar
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
