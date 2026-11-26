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
use Illuminate\Validation\Rule;
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
    public $proyecto, $ano, $n_control, $tipo, $fecha;
    public $origen, $destino, $observaciones;
    public $entregado_por_id, $recibido_por_id;
    public $entregado_por_name = '';
    public $recibido_por_name = '';

    // === EDICIÓN DE SALIDAS EXISTENTES ===
    public $editingId = null;
    public $filtroTipo = '';

    // === PROPIEDADES PARA LOS DETALLES MÚLTIPLES ===
    public $detalles = [];

    // === PROPIEDADES PARA FILTRADO Y AUTOCOMPLETADO ===
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

    // === SUB-MODAL: REGISTRO RÁPIDO DE RESPONSABLE ===
    public $modalQuickResponsableVisible = false;
    public $responsableTarget = '';
    public $nuevo_resp_name = '';
    public $nuevo_resp_cedula = '';
    public $nuevo_resp_email = '';
    public $nuevo_resp_telefono = '';
    public $nuevo_resp_cargo = '';
    public $nuevo_resp_area = '';

    public function openModal()
    {
        $this->resetInput();
        $this->modalFormVisible = true;
        $this->loadInventoryItems();
        $this->initializeFilteredSerials();
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
        $this->modalDetalleVisible = false;
        $this->modalConfirmDelete = false;
        $this->modalQuickResponsableVisible = false;
        $this->selectedSalidaId = null;
    }

    public function openQuickResponsable(string $target)
    {
        $this->resetQuickResponsableForm();
        $this->responsableTarget = $target;
        $this->modalQuickResponsableVisible = true;
    }

    public function closeQuickResponsable()
    {
        $this->modalQuickResponsableVisible = false;
        $this->resetQuickResponsableForm();
    }

    public function resetQuickResponsableForm()
    {
        $this->reset([
            'nuevo_resp_name',
            'nuevo_resp_cedula',
            'nuevo_resp_email',
            'nuevo_resp_telefono',
            'nuevo_resp_cargo',
            'nuevo_resp_area',
        ]);
        $this->resetErrorBag();
    }

    public function storeQuickResponsable()
    {
        $this->validate([
            'nuevo_resp_name'     => 'required|string|max:255|unique:responsables,name',
            'nuevo_resp_cedula'   => 'required|string|max:20|unique:responsables,cedula',
            'nuevo_resp_email'    => 'nullable|email|unique:responsables,email',
            'nuevo_resp_telefono' => 'nullable|string|max:50|unique:responsables,telefono',
            'nuevo_resp_cargo'    => 'nullable|string|max:255',
            'nuevo_resp_area'     => 'nullable|string|max:255',
        ]);

        $responsable = Responsable::create([
            'name'     => $this->nuevo_resp_name,
            'cedula'   => $this->nuevo_resp_cedula,
            'email'    => $this->nuevo_resp_email,
            'telefono' => $this->nuevo_resp_telefono,
            'cargo'    => $this->nuevo_resp_cargo,
            'area'     => $this->nuevo_resp_area,
        ]);

        $formattedLabel = $responsable->name . ' (' . ($responsable->cargo ?: 'N/A') . ')';

        if ($this->responsableTarget === 'entregado') {
            $this->entregado_por_id = $responsable->id;
            $this->entregado_por_name = $formattedLabel;
        } elseif ($this->responsableTarget === 'recibido') {
            $this->recibido_por_id = $responsable->id;
            $this->recibido_por_name = $formattedLabel;
        }

        $this->closeQuickResponsable();
    }

    // === CARGA Y BÚSQUEDA MULTI-CAMPO (CON STOCK DISPONIBLE) ===
    private function loadInventoryItems()
    {
        $configuraciones = [
            'material' => [
                'model'      => Material::class,
                'serial_col' => 'serial',
                'campos'     => ['marca', 'cantidad', 'unidad_medida'],
            ],
            'vehiculo' => [
                'model'      => Vehiculo::class,
                'serial_col' => 'placa',
                'campos'     => ['tipo', 'marca', 'color'],
            ],
            'facilidad' => [
                'model'      => Facilidad::class,
                'serial_col' => 'serial',
                'campos'     => ['marca', 'cantidad'],
            ],
            'maquinaria_fija' => [
                'model'      => MaquinariaFija::class,
                'serial_col' => 'serial',
                'campos'     => ['codigo', 'marca', 'modelo', 'color', 'cantidad'],
            ],
            'sistema' => [
                'model'      => Sistema::class,
                'serial_col' => 'serial',
                'campos'     => ['marca', 'cantidad'],
            ],
        ];

        $items = [];
        foreach ($configuraciones as $type => $config) {
            $model = $config['model'];
            $serialColumn = $config['serial_col'];

            $columnas = array_unique(array_merge(['id', 'descripcion', $serialColumn], $config['campos']));

            $elementos = $model::select($columnas)->get()->map(function ($item) use ($type, $serialColumn) {
                $itemArray = $item->toArray();

                $serialPlaca   = $itemArray[$serialColumn] ?? '';
                $codigo        = $itemArray['codigo'] ?? null;
                $marca         = $itemArray['marca'] ?? null;
                $modelo        = $itemArray['modelo'] ?? null;
                $tipoVehiculo  = $itemArray['tipo'] ?? null;
                $descripcion   = $itemArray['descripcion'] ?? '';
                $stockActual   = (int) ($itemArray['cantidad'] ?? 1);

                $etiqueta = $serialPlaca;
                if (!empty($codigo)) {
                    $etiqueta .= ' | Cód: ' . $codigo;
                }
                if (!empty($descripcion)) {
                    $etiqueta .= ' | ' . $descripcion;
                }
                if (!empty($marca)) {
                    $etiqueta .= ' (' . $marca . ')';
                }
                if (!empty($modelo)) {
                    $etiqueta .= ' [' . $modelo . ']';
                }
                if (!empty($tipoVehiculo)) {
                    $etiqueta .= ' - ' . $tipoVehiculo;
                }
                $etiqueta .= ' [Disp: ' . $stockActual . ']';

                return [
                    'id'                => $itemArray['id'],
                    'item_tipo'         => $type,
                    'item_serial_placa' => $serialPlaca,
                    'codigo'            => $codigo,
                    'descripcion'       => $descripcion,
                    'marca'             => $marca,
                    'modelo'            => $modelo,
                    'tipo'              => $tipoVehiculo,
                    'cantidad'          => $stockActual,
                    'unidad_medida'     => $itemArray['unidad_medida'] ?? (($type === 'vehiculo') ? 'Unidad' : ''),
                    'display_search'    => $etiqueta,
                ];
            })->toArray();

            $items = array_merge($items, $elementos);
        }

        $this->inventarioItemsCache = $items;
    }

    private function initializeFilteredSerials()
    {
        foreach ($this->detalles as $index => $detalle) {
            $tipo = $detalle['item_tipo'] ?? '';
            $this->filteredSerials[$index] = !empty($tipo) ? $this->getSerialsByType($tipo) : [];
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
        if (empty($type)) return [];

        return $this->getInventarioItems()
            ->where('item_tipo', $type)
            ->map(function ($item) {
                return [
                    'serial_placa'   => $item['item_serial_placa'],
                    'display_search' => $item['display_search'],
                    'cantidad'       => $item['cantidad'],
                ];
            })
            ->values()
            ->toArray();
    }

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

    // Hook dedicado para cambios inmediatos en el select de item_tipo
    public function updatedDetallesItemTipo($value, $key)
    {
        $index = (int) $key;

        $this->detalles[$index]['item_tipo'] = $value;
        $this->detalles[$index]['item_serial_placa'] = '';
        $this->detalles[$index]['descripcion'] = '';
        $this->detalles[$index]['cantidad_salida'] = 1;
        $this->detalles[$index]['stock_disponible'] = null;
        $this->detalles[$index]['unidad_medida'] = ($value === 'vehiculo') ? 'Unidad' : '';
        $this->detalles[$index]['error'] = null;

        $this->filteredSerials[$index] = $this->getSerialsByType($value ?? '');
    }

    public function updatedDetalles($value, $key)
    {
        if (strpos($key, '.') === false) return;

        [$index, $field] = explode('.', $key);
        $index = (int) $index;

        if ($field === 'item_tipo') {
            $this->updatedDetallesItemTipo($value, $index);

        } elseif ($field === 'item_serial_placa' && !empty($value)) {
            $searchValue = trim($value);
            $item_tipo = $this->detalles[$index]['item_tipo'];

            $item = $this->getInventarioItems()->first(function($item) use ($searchValue, $item_tipo) {
                if ($item['item_tipo'] !== $item_tipo) {
                    return false;
                }

                return strcasecmp($item['item_serial_placa'], $searchValue) === 0
                    || strcasecmp($item['display_search'], $searchValue) === 0
                    || (!empty($item['codigo']) && strcasecmp($item['codigo'], $searchValue) === 0)
                    || stripos($item['item_serial_placa'], $searchValue) !== false
                    || (!empty($item['codigo']) && stripos($item['codigo'], $searchValue) !== false)
                    || stripos($item['descripcion'], $searchValue) !== false
                    || (!empty($item['marca']) && stripos($item['marca'], $searchValue) !== false);
            });

            if ($item) {
                $this->detalles[$index]['item_serial_placa'] = $item['item_serial_placa'];
                $this->detalles[$index]['stock_disponible']  = (int) $item['cantidad'];
                $this->detalles[$index]['error']             = null;

                $detallesTxt = $item['descripcion'];
                $extras = array_filter([$item['marca'] ?? null, $item['modelo'] ?? null, $item['tipo'] ?? null]);
                if (!empty($extras)) {
                    $detallesTxt .= ' (' . implode(' - ', $extras) . ')';
                }
                $this->detalles[$index]['descripcion'] = $detallesTxt;

                if ($item_tipo === 'material' && !empty($item['unidad_medida'])) {
                    $this->detalles[$index]['unidad_medida'] = $item['unidad_medida'];
                } elseif ($item_tipo === 'vehiculo') {
                    $this->detalles[$index]['cantidad_salida'] = 1;
                    $this->detalles[$index]['unidad_medida'] = 'Unidad';
                }

                if ($this->detalles[$index]['stock_disponible'] <= 0) {
                    $this->detalles[$index]['error'] = "¡Sin stock disponible!";
                }

            } else {
                $this->detalles[$index]['descripcion'] = 'Descripción no encontrada.';
                $this->detalles[$index]['unidad_medida'] = '';
                $this->detalles[$index]['stock_disponible'] = null;
            }

        } elseif ($field === 'cantidad_salida') {
            $solicitado = (int) $value;
            $stock = $this->detalles[$index]['stock_disponible'] ?? null;

            if (!is_null($stock) && $solicitado > $stock) {
                $this->detalles[$index]['error'] = "Supera el stock actual (Máx: {$stock}).";
            } else {
                $this->detalles[$index]['error'] = null;
            }
        }
    }

    protected function rules()
    {
        $uniqueRule = Rule::unique('salidas', 'n_control');
        if ($this->editingId) {
            $uniqueRule->ignore($this->editingId);
        }

        return [
            'tipo' => ['required', Rule::in(['fabweld', 'sarcos_porras'])],
            'n_control' => ['required', 'string', 'max:255', $uniqueRule],
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
            'detalles.*.cantidad_salida' => 'required|integer|min:1',
            'detalles.*.descripcion' => 'required|string|max:255',
            'detalles.*.unidad_medida' => 'nullable|string|max:255',
        ];
    }

    public function addDetalle()
    {
        $newIndex = count($this->detalles);
        $defaultTipo = 'material';

        $this->detalles[] = [
            'item_tipo' => $defaultTipo,
            'item_serial_placa' => '',
            'cantidad_salida' => 1,
            'stock_disponible' => null,
            'descripcion' => '',
            'unidad_medida' => '',
            'error' => null,
        ];

        $this->filteredSerials[$newIndex] = $this->getSerialsByType($defaultTipo);
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

            $this->applyDetalles($salida);

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

    /**
     * Punto de entrada único del formulario: decide si se crea o se actualiza.
     */
    public function save()
    {
        if ($this->editingId) {
            $this->update();
        } else {
            $this->store();
        }
    }

    /**
     * Aplica los detalles del formulario a una salida (creación y edición):
     * valida stock, registra los renglones y descuenta materiales.
     */
    private function applyDetalles(Salida $salida)
    {
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

            if ($item_tipo === 'vehiculo') {
                if ($cantidad !== 1) {
                    $this->detalles[$index]['error'] = "Los vehículos se gestionan por placa individual. La cantidad debe ser 1.";
                    throw new Exception("Vehículo solicitado con cantidad distinta de 1.");
                }

                $vehiculoEnUso = DetalleSalida::where('item_tipo', 'vehiculo')
                    ->where('item_serial_placa', $serial_placa)
                    ->where('salida_id', '!=', $salida->id)
                    ->whereDoesntHave('salida', function ($q) {
                        $q->whereNotNull('fecha_retorno');
                    })
                    ->exists();

                if ($vehiculoEnUso) {
                    $this->detalles[$index]['error'] = "El vehículo con placa {$serial_placa} ya está registrado en otra salida y no ha retornado.";
                    throw new Exception("Vehículo ya registrado en otra salida.");
                }

            } elseif ($item_tipo === 'material') {
                $stockActual = (int) $item->cantidad;

                if ($stockActual < $cantidad) {
                    $this->detalles[$index]['error'] = "Stock insuficiente para {$serial_placa}. Disponible: {$stockActual}. Solicitado: {$cantidad}.";
                    throw new Exception("Stock insuficiente.");
                }

                $item->decrement('cantidad', $cantidad);

            } else {
                if ($cantidad < 1) {
                    $this->detalles[$index]['error'] = "La cantidad debe ser 1 o mayor.";
                    throw new Exception("Cantidad inválida para ítem.");
                }
            }

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
    }

    /**
     * Abre el modal en modo edición con los datos de una salida existente.
     */
    public function openEditModal($salidaId)
    {
        $this->authorize('edit salida');

        $salida = Salida::with('detalles')->findOrFail($salidaId);

        $this->resetInput();

        $this->editingId = $salida->id;
        $this->proyecto = $salida->proyecto;
        $this->ano = $salida->ano;
        $this->n_control = $salida->n_control;
        $this->tipo = $salida->tipo ?: 'fabweld';
        $this->fecha = $salida->fecha;
        $this->origen = $salida->origen;
        $this->destino = $salida->destino;
        $this->observaciones = $salida->observaciones;
        $this->entregado_por_id = $salida->entregado_por_id;
        $this->recibido_por_id = $salida->recibido_por_id;

        $entregado = Responsable::find($salida->entregado_por_id);
        $recibido = Responsable::find($salida->recibido_por_id);
        $this->entregado_por_name = $entregado ? $entregado->name . ' (' . ($entregado->cargo ?: 'N/A') . ')' : '';
        $this->recibido_por_name = $recibido ? $recibido->name . ' (' . ($recibido->cargo ?: 'N/A') . ')' : '';

        $this->detalles = [];
        $this->filteredSerials = [];
        foreach ($salida->detalles as $detalle) {
            $this->detalles[] = [
                'item_tipo' => $detalle->item_tipo,
                'item_serial_placa' => $detalle->item_serial_placa,
                'cantidad_salida' => $detalle->cantidad_salida,
                'stock_disponible' => $this->stockActual($detalle->item_tipo, $detalle->item_serial_placa),
                'descripcion' => $detalle->descripcion,
                'unidad_medida' => $detalle->unidad_medida,
                'error' => null,
            ];
        }
        $this->initializeFilteredSerials();

        $this->modalFormVisible = true;
    }

    /**
     * Stock actual de un material por serial (para mostrar en la edición).
     */
    private function stockActual(string $itemTipo, ?string $serialPlaca): ?int
    {
        if ($itemTipo !== 'material' || empty($serialPlaca)) {
            return null;
        }

        $material = Material::where('serial', $serialPlaca)->first();

        return $material ? (int) $material->cantidad : null;
    }

    /**
     * Actualiza una salida existente: devuelve el stock anterior,
     * reemplaza los detalles y vuelve a validar contra inventario.
     */
    public function update()
    {
        $this->authorize('edit salida');

        if (!$this->editingId) {
            return;
        }

        $this->detalles = array_map(function ($detalle) {
            $detalle['error'] = null;
            return $detalle;
        }, $this->detalles);

        $this->validate();

        try {
            DB::beginTransaction();

            $salida = Salida::with('detalles')->findOrFail($this->editingId);

            // 1) Devuelve el stock consumido por los detalles originales
            foreach ($salida->detalles as $detalle) {
                if ($detalle->item_tipo === 'material') {
                    Material::where('serial', $detalle->item_serial_placa)
                        ->increment('cantidad', (int) $detalle->cantidad_salida);
                }
            }

            // 2) Elimina los detalles anteriores y actualiza la cabecera
            $salida->detalles()->delete();
            $salida->update($this->modelData());

            // 3) Vuelve a aplicar los detalles del formulario (revalida stock)
            $this->applyDetalles($salida);

            DB::commit();
            session()->flash('success', '¡Salida actualizada con éxito!');
            $this->resetInput();
            $this->closeModal();

        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al actualizar la salida: ' . $e->getMessage());
            if (collect($this->detalles)->pluck('error')->filter()->isNotEmpty()) {
                $this->dispatch('alert', ['type' => 'error', 'message' => 'Revise los errores de stock/disponibilidad en la tabla de detalles.']);
            }
        }
    }

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

    public function searchSalidas()
    {
        $this->resetPage();
    }

    public function updatedFiltroTipo()
    {
        $this->resetPage();
    }

    private function resetInput()
    {
        $this->reset([
            'proyecto', 'ano', 'n_control', 'tipo', 'origen', 'destino',
            'observaciones', 'entregado_por_id', 'recibido_por_id', 'detalles',
            'searchEntregado', 'searchRecibido',
            'entregado_por_name', 'recibido_por_name',
            'filteredSerials',
            'modalDetalleVisible',
            'modalConfirmDelete',
            'modalQuickResponsableVisible',
            'selectedSalidaId',
            'detalleSalidaItems',
            'editingId',
        ]);

        $this->tipo = 'fabweld';
        $this->fecha = now()->format('Y-m-d');
        $this->detalles = [];
        $this->loadInventoryItems();
        $this->addDetalle();
        $this->resetValidation();
    }

    private function modelData()
    {
        return [
            'proyecto' => $this->proyecto,
            'ano' => $this->ano,
            'n_control' => $this->n_control,
            'tipo' => $this->tipo,
            'fecha' => $this->fecha,
            'origen' => $this->origen,
            'destino' => $this->destino,
            'observaciones' => $this->observaciones,
            'entregado_por_id' => $this->entregado_por_id,
            'recibido_por_id' => $this->recibido_por_id,
        ];
    }

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
                $salida->delete();
                session()->flash('success', 'Salida eliminada correctamente.');
            }

            $this->modalConfirmDelete = false;
            $this->selectedSalidaId = null;
        }
    }

    public function render()
    {
        $salidas = Salida::query()
            ->when($this->search, function ($query) {
                $search = $this->search;
                $query->where(function ($q) use ($search) {
                    $q->where('n_control', 'like', '%' . $search . '%')
                      ->orWhere('proyecto', 'like', '%' . $search . '%')
                      ->orWhere('origen', 'like', '%' . $search . '%')
                      ->orWhere('ano', 'like', '%' . $search . '%')
                      ->orWhere('fecha', 'like', '%' . $search . '%')
                      ->orWhere('observaciones', 'like', '%' . $search . '%')
                      ->orWhere('destino', 'like', '%' . $search . '%')
                      ->orWhere('tipo', 'like', '%' . $search . '%');
                });
            })
            ->when($this->filtroTipo, function ($query) {
                $query->where('tipo', $this->filtroTipo);
            })
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->paginate);

        return view('livewire.salidas.salida-index', [
            'salidas' => $salidas,
        ]);
    }

    public function exportPdf($salidaId)
    {
        $salida = Salida::with(['entregadoPor', 'recibidoPor', 'detalles'])->findOrFail($salidaId);

        $logoPath = public_path($salida->tipo === 'sarcos_porras' ? 'logo_sarcos.jpg' : 'logo.jpg');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('logo.jpg');
        }
        $mime = pathinfo($logoPath, PATHINFO_EXTENSION) === 'png' ? 'image/png' : 'image/jpeg';
        $logoBase64 = base64_encode(file_get_contents($logoPath));
        $logoData = 'data:' . $mime . ';base64,' . $logoBase64;

        $data = [
            'salida' => $salida,
            'detalles' => $salida->detalles,
            'logoData' => $logoData,
        ];

        $html = View::make('pdf.salida-individual', $data)->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response()->streamDownload(
            fn () => print($dompdf->output()),
            'Salida_'.$salida->n_control.'.pdf'
        );
    }
}
