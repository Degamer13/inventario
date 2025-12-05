<?php

namespace App\Livewire\Salidas;

use Livewire\Component;
use App\Models\Salida;
use App\Models\DetalleSalida;
use App\Models\Responsable;
use App\Models\Material;
use App\Models\Vehiculo;
use App\Models\Facilidad;
use App\Models\Sistema;
use App\Models\MaquinariaFija;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Exception;

class SalidaIndex extends Component
{
    use WithPagination;

    // === PROPIEDADES DE BÚSQUEDA Y LISTADO ===
    public $search = '';
    public $sortColumn = 'fecha';
    public $sortDirection = 'desc';
    protected $queryString = ['search' => ['except' => ''], 'sortColumn', 'sortDirection'];
    public $paginate = 5;

    // === PROPIEDADES DE BÚSQUEDA DE RESPONSABLES ===
    public $searchEntregado = '';
    public $searchRecibido = '';

    // === PROPIEDADES DE LA SALIDA PRINCIPAL ===
    public $proyecto, $ano, $n_control, $fecha;
    public $origen, $destino, $observaciones;
    public $entregado_por_id, $recibido_por_id;
    public $entregado_por_name = ''; // Para autocomplete
    public $recibido_por_name = ''; // Para autocomplete

    // === PROPIEDADES PARA LOS DETALLES MÚLTIPLES ===
    public $detalles = [];

    // 🚀 PROPIEDADES PARA FILTRADO EN CASCADA
    public $filteredSerials = [];
    protected $inventarioItemsCache = null;

    // === PROPIEDADES DE SOPORTE Y MODALES ===
    public $itemTypes = ['material', 'facilidad', 'maquinaria_fija', 'sistema', 'vehiculo'];
    public $modalFormVisible = false;
    public $limitResults = 15;

    // === PROPIEDADES DEL MODAL DE DETALLE Y ELIMINACIÓN ===
    public $selectedSalidaId = null;
    public $detalleSalidaItems = [];
    public $modalDetalleVisible = false;
    public $modalConfirmDelete = false;

    // === CONTROL DE MODAL PRINCIPAL ===
    public function openModal()
    {
        $this->modalFormVisible = true;
        $this->resetInput();
        $this->loadInventoryItems(); // Cargar inventario al abrir el modal
    }

    public function closeModal()
    {
        $this->modalFormVisible = false;
        $this->resetInput();
    }

    public function mount()
    {
        $this->fecha = now()->format('Y-m-d');
        if (empty($this->detalles)) {
            $this->addDetalle();
        }
        // 🛠️ Inicializar variables de modales
        $this->modalDetalleVisible = false;
        $this->modalConfirmDelete = false;
        $this->selectedSalidaId = null;
    }

    // === DATOS DE INVENTARIO (para optimizar la búsqueda) ===
    private function loadInventoryItems()
    {
        $models = [
            'material' => [Material::class, 'serial'],
            'vehiculo' => [Vehiculo::class, 'placa'],
            'facilidad' => [Facilidad::class, 'serial'],
            'maquinaria_fija' => [MaquinariaFija::class, 'serial'],
            'sistema' => [Sistema::class, 'serial'],
        ];

        $items = [];
        foreach ($models as $type => [$model, $serialColumn]) {
            $select = ['id', DB::raw("{$serialColumn} as item_serial_placa"), 'descripcion'];
            if ($model === Material::class) {
                $select[] = 'cantidad';
                $select[] = 'unidad_medida';
            }

            $modelItems = $model::select($select)->get()->map(function ($item) use ($type) {
                $data = $item->toArray();
                $data['item_tipo'] = $type;
                $data['cantidad'] = $data['cantidad'] ?? 1;
                $data['unidad_medida'] = $data['unidad_medida'] ?? (($type === 'vehiculo') ? 'Unidad' : '');
                return $data;
            })->toArray();

            $items = array_merge($items, $modelItems);
        }

        $this->inventarioItemsCache = $items;
        $this->initializeFilteredSerials();
    }

    private function initializeFilteredSerials()
    {
        if (is_array($this->inventarioItemsCache)) {
            foreach ($this->detalles as $index => $detalle) {
                $this->filteredSerials[$index] = $this->getSerialsByType($detalle['item_tipo']);
            }
        }
    }

    private function getInventarioItems()
    {
        if (is_null($this->inventarioItemsCache)) {
            $this->loadInventoryItems();
        }
        return collect($this->inventarioItemsCache);
    }

    private function getSerialsByType(string $type)
    {
        return $this->getInventarioItems()
            ->where('item_tipo', $type)
            ->pluck('item_serial_placa')
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }
    // === FIN DATOS DE INVENTARIO ===

    // === LISTAR RESPONSABLES (Mantenido) ===
    public function getResponsablesProperty()
    {
        $search = $this->searchEntregado ?: $this->searchRecibido;
        $query = Responsable::select('id', 'name', 'cargo');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('cargo', 'like', '%' . $search . '%');
            })->limit(20);
        } else {
            $query->limit($this->limitResults);
        }

        return $query->orderBy('name')->get();
    }

    // === LISTENERS PARA EL FILTRADO Y AUTOCOMPLETADO 🚀 ===
    public function updatedDetalles($value, $key)
    {
        if (strpos($key, '.') === false) return;

        [$index, $field] = explode('.', $key);
        $index = (int) $index;

        if ($field === 'item_tipo') {
            $this->detalles[$index]['item_serial_placa'] = '';
            $this->detalles[$index]['descripcion'] = '';

            if ($value === 'vehiculo') {
                $this->detalles[$index]['cantidad_salida'] = 1;
                $this->detalles[$index]['unidad_medida'] = 'Unidad';
            } else {
                $this->detalles[$index]['cantidad_salida'] = 1;
                $this->detalles[$index]['unidad_medida'] = '';
            }

            $this->filteredSerials[$index] = $this->getSerialsByType($value);

        } elseif ($field === 'item_serial_placa' && !empty($value)) {
            $selectedSerial = $value;
            $item_tipo = $this->detalles[$index]['item_tipo'];

            $item = $this->getInventarioItems()->first(function($item) use ($selectedSerial, $item_tipo) {
                return $item['item_serial_placa'] === $selectedSerial && $item['item_tipo'] === $item_tipo;
            });

            if ($item) {
                $this->detalles[$index]['descripcion'] = $item['descripcion'];

                if ($item_tipo === 'material' && isset($item['unidad_medida'])) {
                    $this->detalles[$index]['unidad_medida'] = $item['unidad_medida'];
                } elseif ($item_tipo === 'vehiculo') {
                    $this->detalles[$index]['cantidad_salida'] = 1;
                    $this->detalles[$index]['unidad_medida'] = 'Unidad';
                }
            } else {
                $this->detalles[$index]['descripcion'] = 'Descripción no encontrada.';
                $this->detalles[$index]['unidad_medida'] = '';
            }
        }
    }


    // === VALIDACIONES (Mantenido) ===
    protected function rules()
    {
        return [
            'n_control' => 'required|string|max:255|unique:salidas,n_control',
            'fecha' => 'required|date',
            'entregado_por_id' => 'required|exists:responsables,id',
            'recibido_por_id' => 'required|exists:responsables,id',
            'proyecto' => 'nullable|string|max:255',
            'ano' => 'nullable|integer',
            'origen' => 'nullable|string|max:255',
            'destino' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.item_tipo' => 'required|in:' . implode(',', $this->itemTypes),
            'detalles.*.item_serial_placa' => 'required|string|max:255',
            'detalles.*.cantidad_salida' => 'required|integer|min:1', // Cantidad >= 1
            'detalles.*.descripcion' => 'required|string|max:255',
            'detalles.*.unidad_medida' => 'nullable|string|max:255',
        ];
    }

    // === AÑADIR Y ELIMINAR DETALLES ===
    public function addDetalle()
    {
        $newIndex = count($this->detalles);
        $this->detalles[] = [
            'item_tipo' => 'material',
            'item_serial_placa' => '',
            'cantidad_salida' => 1,
            'descripcion' => '',
            'unidad_medida' => '',
            'error' => null,
        ];

        if (!is_null($this->inventarioItemsCache)) {
            $this->filteredSerials[$newIndex] = $this->getSerialsByType('material');
        } else {
             $this->filteredSerials[$newIndex] = [];
        }
    }

    public function removeDetalle($index)
    {
        unset($this->detalles[$index]);
        $this->detalles = array_values($this->detalles);

        if (isset($this->filteredSerials[$index])) {
             unset($this->filteredSerials[$index]);
             $this->filteredSerials = array_values($this->filteredSerials);
        }
    }

    // === GUARDAR SALIDA (Descuento de stock y validación de cantidad corregida) ===
    public function store()
    {
        $this->detalles = array_map(function ($detalle) {
            $detalle['error'] = null;
            return $detalle;
        }, $this->detalles);

        $this->validate();

        try {
            DB::beginTransaction();

            $salida = Salida::create($this->modelData());

            foreach ($this->detalles as $index => $detalleData) {
                $item_tipo = $detalleData['item_tipo'];
                $serial_placa = $detalleData['item_serial_placa'];
                $cantidad = (int) $detalleData['cantidad_salida'];

                $modelMap = [
                    'material' => [Material::class, 'serial'],
                    'vehiculo' => [Vehiculo::class, 'placa'],
                    'facilidad' => [Facilidad::class, 'serial'],
                    'maquinaria_fija' => [MaquinariaFija::class, 'serial'],
                    'sistema' => [Sistema::class, 'serial'],
                ];

                if (!isset($modelMap[$item_tipo])) {
                    $this->detalles[$index]['error'] = "Tipo de ítem inválido: {$item_tipo}.";
                    throw new Exception("Tipo de ítem inválido.");
                }

                [$modelClass, $searchColumn] = $modelMap[$item_tipo];
                $item = $modelClass::where($searchColumn, $serial_placa)->first();

                if (!$item) {
                    $this->detalles[$index]['error'] = "Ítem no encontrado con {$searchColumn}: {$serial_placa}.";
                    throw new Exception("Ítem no encontrado.");
                }

                // 🛠️ Lógica de Descuento y Validación
                if ($item_tipo === 'vehiculo') {
                    if ($cantidad !== 1) {
                        $this->detalles[$index]['error'] = "Los vehículos se gestionan por placa individual. La cantidad debe ser exactamente 1.";
                        throw new Exception("Vehículo solicitado con cantidad distinta de 1.");
                    }

                    $vehiculoEnUso = DetalleSalida::where('item_tipo', 'vehiculo')
                        ->where('item_serial_placa', $serial_placa)
                        ->whereDoesntHave('salida', function ($q) {
                             $q->whereNotNull('fecha_retorno');
                        })
                        ->exists();

                    if ($vehiculoEnUso) {
                        $this->detalles[$index]['error'] = "El vehículo con placa {$serial_placa} ya está registrado en otra salida y no ha retornado.";
                        throw new Exception("Vehículo ya registrado en otra salida.");
                    }

                } elseif ($item_tipo === 'material') {
                    // ✅ DESCUENTO Y VALIDACIÓN DE STOCK PARA MATERIALES

                    $stockActual = (int) $item->cantidad;

                    if ($stockActual < $cantidad) {
                        $this->detalles[$index]['error'] = "Stock insuficiente para {$serial_placa}. Disponible: {$stockActual}. Solicitado: {$cantidad}.";
                        throw new Exception("Stock insuficiente.");
                    }

                    $item->decrement('cantidad', $cantidad); // 🚀 ACCIÓN DE DESCUENTO

                } else {
                    // Otros ítems (Facilidad, Maquinaria Fija, Sistema)
                    if ($cantidad < 1) {
                         $this->detalles[$index]['error'] = "La cantidad debe ser 1 o mayor.";
                         throw new Exception("Cantidad inválida para ítem.");
                    }
                    // Si tienes lógica de estado para estos ítems, iría aquí (e.g., cambiar estado a 'En uso')
                }
                // === FIN Lógica de Descuento y Validación ===


                DetalleSalida::create([
                    'salida_id' => $salida->id,
                    'item_tipo' => $item_tipo,
                    'item_id' => $item->id,
                    'item_serial_placa' => $serial_placa,
                    'descripcion' => $detalleData['descripcion'],
                    'cantidad_salida' => $cantidad,
                    'unidad_medida' => $detalleData['unidad_medida'],
                ]);
            }

            DB::commit();
            session()->flash('success', '¡Salida registrada con éxito!');
            $this->resetInput();
            $this->closeModal();

        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al registrar la salida: ' . $e->getMessage());
            if (collect($this->detalles)->pluck('error')->filter()->isNotEmpty()) {
                $this->dispatch('alert', ['type' => 'error', 'message' => 'Revise los errores de stock/disponibilidad en la tabla de detalles.']);
            }
        }
    }
    // === FIN GUARDAR SALIDA ===

    // === ORDENAR COLUMNAS (Mantenido) ===
    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
        $this->gotoPage(1);
    }

    // === BUSCAR SALIDAS (Mantenido) ===
    public function searchSalidas()
    {
        $this->resetPage();
    }

    // === LIMPIAR CAMPOS (Corregido para manejar todas las propiedades) ===
    private function resetInput()
    {
        $this->reset([
            'proyecto', 'ano', 'n_control', 'fecha', 'origen', 'destino',
            'observaciones', 'entregado_por_id', 'recibido_por_id', 'detalles',
            'searchEntregado', 'searchRecibido',
            'entregado_por_name', 'recibido_por_name',
            'filteredSerials',
            'modalDetalleVisible',
            'modalConfirmDelete',
            'selectedSalidaId',
            'detalleSalidaItems'
        ]);
        $this->resetValidation();
        $this->mount();
        $this->inventarioItemsCache = null;
    }

    // === DATOS DE MODELO (Mantenido) ===
    private function modelData()
    {
        return [
            'proyecto' => $this->proyecto,
            'ano' => $this->ano,
            'n_control' => $this->n_control,
            'fecha' => $this->fecha,
            'origen' => $this->origen,
            'destino' => $this->destino,
            'observaciones' => $this->observaciones,
            'entregado_por_id' => $this->entregado_por_id,
            'recibido_por_id' => $this->recibido_por_id,
        ];
    }

    // === MODAL DE DETALLES (Mantenido) ===
    public function openDetalleModal($salidaId)
    {
        $this->selectedSalidaId = $salidaId;
        $this->detalleSalidaItems = DetalleSalida::where('salida_id', $salidaId)->get();
        $this->modalDetalleVisible = true;
    }

    public function closeDetalleModal()
    {
        $this->modalDetalleVisible = false;
        $this->detalleSalidaItems = [];
        $this->selectedSalidaId = null;
    }

    // === MODAL DE CONFIRMACIÓN DE ELIMINACIÓN (Mantenido) ===
    public function confirmDelete($id)
    {
        $this->selectedSalidaId = $id;
        $this->modalConfirmDelete = true;
    }

    public function delete()
    {
        if ($this->selectedSalidaId) {
            $salida = Salida::find($this->selectedSalidaId);

            if ($salida) {
                // Lógica de reversión de stock/estado debería ir aquí si se elimina la salida
                $salida->delete();
                session()->flash('success', 'Salida eliminada correctamente.');
            }

            $this->modalConfirmDelete = false;
            $this->selectedSalidaId = null;
        }
    }

    // === RENDER (Mantenido) ===
    public function render()
    {
        $salidas = Salida::query()
            ->when($this->search, function ($query) {
                $query->where('n_control', 'like', '%' . $this->search . '%')
                      ->orWhere('proyecto', 'like', '%' . $this->search . '%')
                      ->orWhere('origen', 'like', '%' . $this->search . '%')
                      ->orWhere('destino', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->paginate);

        return view('livewire.salidas.salida-index', [
            'salidas' => $salidas,
        ]);
    }

    public function exportPdf($salidaId)
    {
        // Cargar la salida con sus relaciones correctas
        $salida = Salida::with(['entregadoPor', 'recibidoPor', 'detalles'])->findOrFail($salidaId);

        // Cargar logo y convertirlo a Base64
        $logoPath = public_path('logo.jpg');
        $logoBase64 = base64_encode(file_get_contents($logoPath));
        $logoData = 'data:image/jpeg;base64,' . $logoBase64;

        // Preparar los datos para la vista PDF
        $data = [
            'salida' => $salida,
            'detalles' => $salida->detalles, // ya vienen cargados
            'logoData' => $logoData,
        ];

        // Renderizar la vista a HTML
        $html = View::make('pdf.salida-individual', $data)->render();

        // Configurar DomPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // Muy importante

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Descargar el PDF
        return response()->streamDownload(
            fn () => print($dompdf->output()),
            'Salida_'.$salida->n_control.'.pdf'
        );
    }
}
