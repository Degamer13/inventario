<?php

namespace App\Livewire\Salidas;

use Livewire\Component;
use App\Models\Salida;
use App\Models\DetalleSalida;
use App\Models\Responsable;
// Importa los modelos de inventario
use App\Models\Material;
use App\Models\Vehiculo;
use App\Models\Facilidad;
use App\Models\Sistema;
use App\Models\MaquinariaFija;
use Illuminate\Support\Facades\DB;
use Exception;

class SalidaIndex extends Component
{
    // === PROPIEDADES DE BÚSQUEDA ===
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
    public $modalFormVisible = true;
    public $limitResults = 15; // Nuevo límite para mostrar cuando no hay búsqueda activa.

    public function mount()
    {
        $this->fecha = now()->format('Y-m-d');
        // Inicializar al menos un campo de detalle
        $this->addDetalle();
    }

    /**
     * Propiedad Computada: Obtiene la lista de responsables.
     * Muestra resultados limitados incluso si no hay término de búsqueda.
     */
    public function getResponsablesProperty()
    {
        // Se usa la búsqueda activa (entregado o recibido) para filtrar.
        $search = $this->searchEntregado ?: $this->searchRecibido;

        $query = Responsable::select('id', 'name', 'cargo');

        // 🚩 LÓGICA MODIFICADA: Si hay un término de búsqueda (aunque sea 1 o 2 chars), filtramos.
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('cargo', 'like', '%' . $search . '%');
            });
            // Si hay búsqueda, aplicamos el límite (por si hay miles de responsables)
            $query->limit(20);

        } else {
            // Si el campo de búsqueda está vacío, devolvemos los primeros 15 resultados
            // para que se muestren al abrir el dropdown. (ej. tus 11 registros)
            $query->limit($this->limitResults);
        }

        return $query->orderBy('name')->get();
    }

    protected function rules()
    {
        return [
            // Reglas para la cabecera de la Salida
            'n_control' => 'required|string|max:255|unique:salidas,n_control',
            'fecha' => 'required|date',
            'entregado_por_id' => 'required|exists:responsables,id',
            'recibido_por_id' => 'required|exists:responsables,id',
            'proyecto' => 'nullable|string|max:255',
            'ano' => 'nullable|integer',
            'origen' => 'nullable|string|max:255',
            'destino' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',

            // Reglas para los Detalles Múltiples
            'detalles' => 'required|array|min:1',
            'detalles.*.item_tipo' => 'required|in:' . implode(',', $this->itemTypes),
            'detalles.*.item_serial_placa' => 'required|string|max:255',
            'detalles.*.cantidad_salida' => 'required|integer|min:1',
            'detalles.*.descripcion' => 'required|string|max:255',
            'detalles.*.unidad_medida' => 'nullable|string|max:255',
        ];
    }

    // Método para añadir una nueva fila de detalle
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

    // Método para remover una fila de detalle
    public function removeDetalle($index)
    {
        unset($this->detalles[$index]);
        $this->detalles = array_values($this->detalles);
    }

    /**
     * Registra la salida, sus detalles y actualiza el inventario.
     */
    public function store()
    {
        // Limpiar errores del backend antes de validar
        $this->detalles = array_map(function($detalle) {
            $detalle['error'] = null;
            return $detalle;
        }, $this->detalles);

        $this->validate();

        try {
            DB::beginTransaction();

            // 1. Crear la Salida principal
            $salida = Salida::create($this->modelData());

            // 2. Procesar los Detalles y Actualizar el Inventario
            foreach ($this->detalles as $index => $detalleData) {
                $item_tipo = $detalleData['item_tipo'];
                $serial_placa = $detalleData['item_serial_placa'];
                $cantidad = $detalleData['cantidad_salida'];

                // Mapeo de tipo de ítem a Modelo y Columna clave
                $modelMap = [
                    'material' => [Material::class, 'serial'],
                    'vehiculo' => [Vehiculo::class, 'placa'],
                    'facilidad' => [Facilidad::class, 'serial'],
                    'maquinaria_fija' => [MaquinariaFija::class, 'serial'],
                    'sistema' => [Sistema::class, 'serial'],
                ];

                list($modelClass, $searchColumn) = $modelMap[$item_tipo];

                // Buscar el ítem por Serial o Placa
                $item = $modelClass::where($searchColumn, $serial_placa)->first();

                if (!$item) {
                    $this->detalles[$index]['error'] = "Ítem no encontrado con {$searchColumn}: {$serial_placa}.";
                    throw new Exception("Ítem no encontrado.");
                }

                // Lógica de Descuento / Actualización de Ubicación
                if ($item_tipo === 'vehiculo' || $item_tipo === 'facilidad' || $item_tipo === 'maquinaria_fija' || $item_tipo === 'sistema') {
                    // Items unitarios (vehículo, maquinaria, etc.): actualiza la ubicación
                    $item->update(['ubicacion' => $salida->destino]);

                    // Si se está "sacando" un ítem que se considera unitario, la cantidad debe ser 1
                    if ($cantidad > 1) {
                         $this->detalles[$index]['error'] = "Este ítem es unitario. La cantidad debe ser 1.";
                         throw new Exception("Ítem unitario, cantidad > 1.");
                    }
                } else {
                    // MATERIAL (Inventariable): Descuenta la cantidad
                    if ($item->cantidad < $cantidad) {
                        $this->detalles[$index]['error'] = "Stock insuficiente. Disponible: {$item->cantidad}. Solicitado: {$cantidad}.";
                        throw new Exception("Stock insuficiente.");
                    }
                    $item->decrement('cantidad', $cantidad);
                }

                // 3. Crear el Detalle de Salida
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

            session()->flash('success', '¡Salida registrada e inventario actualizado con éxito!');
            $this->resetInput();

        } catch (Exception $e) {
            DB::rollBack();
            // Mantener el mensaje de error de sesión, ya que los errores específicos están en $detalles
            session()->flash('error', 'Error al registrar la salida. Revise los errores en la lista de ítems o contacte soporte: ' . $e->getMessage());
        }
    }

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

    public function render()
    {
        return view('livewire.salidas.salida-index');
    }
}
