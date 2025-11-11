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

    // === PROPIEDADES PARA LOS DETALLES MÚLTIPLES ===
    public $detalles = [];

    // === PROPIEDADES DE SOPORTE ===
    public $itemTypes = ['material', 'facilidad', 'maquinaria_fija', 'sistema', 'vehiculo'];
    public $modalFormVisible = false;
    public $limitResults = 15;

    // === CONTROL DE MODAL PRINCIPAL ===
    public function openModal()
    {
        $this->modalFormVisible = true;
        $this->resetInput();
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
    }

    // === LISTAR RESPONSABLES ===
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

    // === VALIDACIONES ===
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
            'detalles.*.cantidad_salida' => 'required|integer|min:1',
            'detalles.*.descripcion' => 'required|string|max:255',
            'detalles.*.unidad_medida' => 'nullable|string|max:255',
        ];
    }

    // === AÑADIR Y ELIMINAR DETALLES ===
    public function addDetalle()
    {
        $this->detalles[] = [
            'item_tipo' => 'material',
            'item_serial_placa' => '',
            'cantidad_salida' => 1,
            'descripcion' => '',
            'unidad_medida' => '',
            'error' => null,
        ];
    }

    public function removeDetalle($index)
    {
        unset($this->detalles[$index]);
        $this->detalles = array_values($this->detalles);
    }

    // === GUARDAR SALIDA ===
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

                $hasCantidadColumn = array_key_exists('cantidad', $item->getAttributes());

                // === VALIDACIÓN ESPECIAL PARA VEHÍCULOS ===
                if ($item_tipo === 'vehiculo') {
                    if ($cantidad > 1) {
                        $this->detalles[$index]['error'] = "Los vehículos se gestionan por placa individual. La cantidad debe ser 1.";
                        throw new Exception("Vehículo solicitado con cantidad > 1.");
                    }

                    $vehiculoEnUso = DetalleSalida::where('item_tipo', 'vehiculo')
                        ->where('item_serial_placa', $serial_placa)
                        ->exists();

                    if ($vehiculoEnUso) {
                        $this->detalles[$index]['error'] = "El vehículo con placa {$serial_placa} ya está registrado en otra salida.";
                        throw new Exception("Vehículo ya registrado en otra salida.");
                    }
                } else {
                    if ($hasCantidadColumn) {
                        $stockActual = (int) $item->cantidad;
                        if ($stockActual < $cantidad) {
                            $this->detalles[$index]['error'] = "Stock insuficiente para {$serial_placa}. Disponible: {$stockActual}. Solicitado: {$cantidad}.";
                            throw new Exception("Stock insuficiente.");
                        }
                        $item->decrement('cantidad', $cantidad);
                    } else {
                        if ($cantidad > 1) {
                            $this->detalles[$index]['error'] = "Este ítem no tiene control por cantidad. La cantidad debe ser 1.";
                            throw new Exception("Ítem sin control de cantidad.");
                        }
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

            DB::commit();
            session()->flash('success', '¡Salida registrada con éxito!');
            $this->resetInput();
            $this->closeModal();

        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al registrar la salida: ' . $e->getMessage());
        }
    }

    // === ORDENAR COLUMNAS ===
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

    // === BUSCAR SALIDAS ===
    public function searchSalidas()
    {
        $this->resetPage();
    }

    // === LIMPIAR CAMPOS ===
    private function resetInput()
    {
        $this->reset([
            'proyecto', 'ano', 'n_control', 'fecha', 'origen', 'destino',
            'observaciones', 'entregado_por_id', 'recibido_por_id', 'detalles',
            'searchEntregado', 'searchRecibido'
        ]);
        $this->resetValidation();
        $this->mount();
    }

    // === DATOS DE MODELO ===
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

    // === MODAL DE DETALLES ===
    public $selectedSalidaId = null;
    public $detalleSalidaItems = [];
    public $modalDetalleVisible = false;

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

    // === MODAL DE CONFIRMACIÓN DE ELIMINACIÓN ===
    public $modalConfirmDelete = false;

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

    // === RENDER ===
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
}
