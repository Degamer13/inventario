<?php

namespace App\Livewire\BackupManager;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use ZipArchive; // Necesario para abrir el backup

class BackupManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $statusMessage = '';
    public $isError = false;

    public $modalConfirmDelete = false;
    public $modalRestoreVisible = false;
    public $backupToDelete = null;
    public $zipFile;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch() { $this->resetPage(); }

    public function restoreBackup()
    {
        $this->validate([
            'zipFile' => 'required|mimes:zip|max:102400',
        ]);

        try {
            // 1. Guardar el archivo temporalmente
            $fileName = $this->zipFile->getClientOriginalName();
            $path = $this->zipFile->storeAs('temp', $fileName, 'local');
            $fullPath = storage_path('app/private/' . $path);

            // 2. Intentar extraer el contenido
            $zip = new ZipArchive;
            if ($zip->open($fullPath) === TRUE) {
                // Buscamos el archivo .sql dentro del zip
                $sqlFileName = null;
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $stat = $zip->statIndex($i);
                    if (str_ends_with($stat['name'], '.sql')) {
                        $sqlFileName = $stat['name'];
                        break;
                    }
                }

                if (!$sqlFileName) {
                    throw new \Exception("No se encontró ningún archivo .sql dentro del respaldo.");
                }

                // Extraemos el SQL a una carpeta temporal
                $zip->extractTo(storage_path('app/private/temp'), $sqlFileName);
                $zip->close();

                $sqlPath = storage_path('app/private/temp/' . $sqlFileName);

                // 3. EJECUTAR RESTAURACIÓN EN LA BASE DE DATOS
                $mysqlBin = 'C:\\laragon\\bin\\mysql\\mysql-8.4.3-winx64\\bin\\mysql.exe';
                $dbName = env('DB_DATABASE', 'inventario_db');
                $dbUser = env('DB_USERNAME', 'root');
                $dbPass = env('DB_PASSWORD', '');

                // Comando: mysql -u usuario -pPassword base_de_datos < archivo.sql
                $command = "\"{$mysqlBin}\" -u {$dbUser} " . ($dbPass ? "-p{$dbPass} " : "") . "{$dbName} < \"{$sqlPath}\" 2>&1";

                exec($command, $output, $exitCode);

                if ($exitCode === 0) {
                    // Si todo salió bien, movemos el zip a la carpeta de backups para que quede registro
                    Storage::disk('local')->move($path, 'InventarioBackup/' . $fileName);
                    $this->statusMessage = "¡Base de datos restaurada con éxito desde {$fileName}!";
                    $this->isError = false;
                } else {
                    throw new \Exception("Error de MySQL (Código {$exitCode}): " . implode("\n", $output));
                }

                // Limpiar el SQL temporal
                unlink($sqlPath);
            } else {
                throw new \Exception("No se pudo abrir el archivo ZIP.");
            }

            $this->modalRestoreVisible = false;
            $this->reset(['zipFile']);
            $this->resetPage();

        } catch (\Exception $e) {
            $this->isError = true;
            $this->statusMessage = 'Error en restauración: ' . $e->getMessage();
            Log::error($e->getMessage());
        }
    }

    // ... (Mantén tus funciones de generateBackup, downloadBackup, deleteBackup y render igual que antes) ...

    public function generateBackup()
    {
        $this->statusMessage = '';
        $this->isError = false;

        try {
            $mysqlPath = 'C:\\laragon\\bin\\mysql\\mysql-8.4.3-winx64\\bin';
            putenv("PATH=" . getenv("PATH") . ";" . $mysqlPath);
            $phpPath = PHP_BINARY;
            $command = 'cd "' . base_path() . '" && "' . $phpPath . '" artisan backup:run 2>&1';

            exec($command, $output, $exitCode);
            $outputText = implode("\n", $output);

            if (strpos($outputText, 'completed') !== false || $exitCode === 0) {
                $this->statusMessage = '¡El respaldo se generó correctamente!';
                $this->resetPage();
            } else {
                $this->isError = true;
                $this->statusMessage = 'Fallo al generar el respaldo.';
            }
        } catch (\Exception $e) {
            $this->isError = true;
            $this->statusMessage = 'Error crítico: ' . $e->getMessage();
        }
    }

    public function downloadBackup($path) { return Storage::disk('local')->download($path); }

    public function confirmDelete($path) { $this->backupToDelete = $path; $this->modalConfirmDelete = true; }

    public function deleteBackup()
    {
        if ($this->backupToDelete && Storage::disk('local')->exists($this->backupToDelete)) {
            Storage::disk('local')->delete($this->backupToDelete);
            $this->statusMessage = 'Respaldo eliminado.';
        }
        $this->modalConfirmDelete = false;
        $this->resetPage();
    }

    public function render()
    {
        $disk = Storage::disk('local');
        $files = $disk->files('InventarioBackup');
        $allBackups = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                $name = basename($file);
                $dateString = str_replace('.zip', '', $name);
                try {
                    $date = Carbon::createFromFormat('Y-m-d-H-i-s', $dateString)->format('d/m/Y h:i A');
                } catch (\Exception $e) {
                    $date = Carbon::createFromTimestamp($disk->lastModified($file))->timezone('America/Caracas')->format('d/m/Y h:i A');
                }

                if (empty($this->search) || stripos($name, $this->search) !== false || stripos($date, $this->search) !== false) {
                    $allBackups[] = [
                        'name' => $name, 'path' => $file, 'size' => $this->formatBytes($disk->size($file)),
                        'date' => $date, 'timestamp' => $disk->lastModified($file)
                    ];
                }
            }
        }

        $collection = collect($allBackups)->sortByDesc('timestamp');
        $currentPage = $this->paginators['page'] ?? 1;
        $perPage = 5;
        $currentItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $paginatedBackups = new LengthAwarePaginator(
            $currentItems, $collection->count(), $perPage, $currentPage,
            ['path' => url()->current()]
        );

        return view('livewire.backup-manager.backup-manager', ['backups' => $paginatedBackups]);
    }

    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }
}
