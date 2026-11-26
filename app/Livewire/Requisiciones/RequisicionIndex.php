<?php

namespace App\Livewire\Requisiciones;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Requisicion;
use App\Models\DetalleRequisicion;
use App\Models\Material;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;
use Exception;

class RequisicionIndex extends Component
{
    use WithPagination;

    // === PROPIEDADES DE BÚSQUEDA Y FILTROS ===
    public $search = '';
    public $filtroEstado = '';
    public $filtroTipo = '';
    public $filtroPrioridad = '';
    protected $queryString = ['search' => ['except' => ''], 'filtroEstado', 'filtroTipo', 'filtroPrioridad'];
    public $paginate = 5;

    // === PROPIEDADES DEL FORMULARIO DE REQUISICIÓN ===
    public $tipo = 'fabweld';
    public $proyecto = '';
    public $fecha = '';
    public $centro_costos = '';
    public $unidad_solicitante = '';
    public $prioridad = 'intermedio';
    public $observaciones = '';

    // === DETALLES (RENGLONES) DE LA REQUISICIÓN ===
    public $detalles = [];

    // === SOPORTE DE MODALES ===
    public $modalFormVisible = false;
    public $editingId = null;
    public $editingNRequisicion = null;
    public $selectedId = null;
    public $modalDetalleVisible = false;
    public $modalVerificarVisible = false;
    public $modalRechazoVisible = false;
    public $modalConfirmDelete = false;
    public $modalInstructivoVisible = false;
    public $modalEntregaVisible = false;

    // === VERIFICACIÓN CONTRA INVENTARIO (ADMINISTRADOR) ===
    public $verificacion = [];   // Por renglón: ['id','existe','material_id','stock_actual','descripcion']
    public $sugerencias = [];    // Sugerencias de materiales por renglón
    public $materialesOptions = [];

    // === RECHAZO (PLANIFICADOR) ===
    public $motivoRechazo = '';

    // === CONTROL DE ENTREGA (ADMINISTRADOR / PROCURA) ===
    public $entregaItems = [];   // Por renglón: ['id','item','descripcion','cantidad','unidad_medida','entregado','fecha']

    public $tipos = ['fabweld' => 'Fabweld', 'sarcos_porras' => 'Sarcos Porras'];
    public $prioridades = ['urgente' => 'Urgente', 'intermedio' => 'Intermedio', 'bajo' => 'Bajo'];
    public $estados = ['pendiente' => 'Pendiente', 'verificada' => 'Verificada', 'aprobada' => 'Aprobada', 'rechazada' => 'Rechazada'];

    public function mount()
    {
        $this->fecha = now()->format('Y-m-d');
        $this->modalFormVisible = false;
        $this->modalDetalleVisible = false;
        $this->modalVerificarVisible = false;
        $this->modalRechazoVisible = false;
        $this->modalConfirmDelete = false;
        $this->modalInstructivoVisible = false;
        $this->modalEntregaVisible = false;
    }

    // === INSTRUCTIVO OFICIAL 00-PC-F-01 ===

    public function openInstructivo()
    {
        $this->modalInstructivoVisible = true;
    }

    public function closeInstructivo()
    {
        $this->modalInstructivoVisible = false;
    }

    // === HERRAMIENTAS DE ROL ===

    /**
     * Los administradores, planificadores y usuarios de procura ven todas las
     * requisiciones; los usuarios normales solo las propias.
     */
    public function isManager(): bool
    {
        return Auth::user()->hasAnyRole(['admin', 'planificador', 'procura']);
    }

    // === FORMULARIO CREAR / EDITAR ===

    public function openModal()
    {
        $this->resetInput();
        $this->modalFormVisible = true;
    }

    public function closeModal()
    {
        $this->modalFormVisible = false;
        $this->resetInput();
    }

    public function addDetalle()
    {
        $this->detalles[] = [
            'cantidad' => 1,
            'unidad_medida' => '',
            'descripcion' => '',
            'certificado_calidad' => false,
            'proveedor_sugerido' => '',
            'observaciones' => '',
        ];
    }

    public function removeDetalle($index)
    {
        unset($this->detalles[$index]);
        $this->detalles = array_values($this->detalles);
    }

    protected function rules()
    {
        return [
            'tipo' => ['required', Rule::in(['fabweld', 'sarcos_porras'])],
            'fecha' => 'required|date',
            'proyecto' => 'nullable|string|max:255',
            'centro_costos' => 'nullable|string|max:255',
            'unidad_solicitante' => 'nullable|string|max:255',
            'prioridad' => ['required', Rule::in(['urgente', 'intermedio', 'bajo'])],
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.unidad_medida' => 'nullable|string|max:100',
            'detalles.*.descripcion' => 'required|string|max:2000',
            'detalles.*.certificado_calidad' => 'nullable|boolean',
            'detalles.*.proveedor_sugerido' => 'nullable|string|max:255',
            'detalles.*.observaciones' => 'nullable|string|max:2000',
        ];
    }

    private function normalizarDetalles(): array
    {
        return collect($this->detalles)->map(function ($detalle, $index) {
            return [
                'item' => $index + 1,
                'cantidad' => (float) ($detalle['cantidad'] ?? 0),
                'unidad_medida' => $detalle['unidad_medida'] ?? '',
                'descripcion' => trim($detalle['descripcion'] ?? ''),
                'certificado_calidad' => !empty($detalle['certificado_calidad']),
                'proveedor_sugerido' => $detalle['proveedor_sugerido'] ?? '',
                'observaciones' => $detalle['observaciones'] ?? '',
            ];
        })->toArray();
    }

    /**
     * Genera el número de control: RQ-{centro_costos}-{correlativo de 3 dígitos}
     */
    private function generarNRequisicion(): string
    {
        $base = 'RQ-' . strtoupper(trim($this->centro_costos ?: 'GEN')) . '-';

        $ultimo = Requisicion::where('n_requisicion', 'like', $base . '%')
            ->orderByDesc('n_requisicion')
            ->value('n_requisicion');

        $correlativo = 1;
        if ($ultimo) {
            $partes = explode('-', $ultimo);
            $correlativo = (int) end($partes) + 1;
        }

        return $base . str_pad((string) $correlativo, 3, '0', STR_PAD_LEFT);
    }

    public function store()
    {
        $this->authorize('create requisicion');

        $this->validate();

        try {
            DB::beginTransaction();

            $nRequisicion = $this->generarNRequisicion();

            // Evita colisiones de correlativo
            $intento = 0;
            while (Requisicion::where('n_requisicion', $nRequisicion)->exists() && $intento < 50) {
                $nRequisicion = $this->generarNRequisicion();
                $intento++;
            }

            $requisicion = Requisicion::create([
                'tipo' => $this->tipo,
                'n_requisicion' => $nRequisicion,
                'proyecto' => $this->proyecto,
                'fecha' => $this->fecha,
                'centro_costos' => $this->centro_costos,
                'unidad_solicitante' => $this->unidad_solicitante,
                'prioridad' => $this->prioridad,
                'estado' => 'pendiente',
                'observaciones' => $this->observaciones,
                'preparado_por_id' => Auth::id(),
            ]);

            foreach ($this->normalizarDetalles() as $detalle) {
                $requisicion->detalles()->create($detalle);
            }

            DB::commit();
            session()->flash('success', '¡Requisición ' . $nRequisicion . ' registrada con éxito!');
            $this->closeModal();

        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al registrar la requisición: ' . $e->getMessage());
        }
    }

    public function openEditModal($id)
    {
        $this->authorize('edit requisicion');

        $requisicion = Requisicion::with('detalles')->findOrFail($id);

        if ($requisicion->estado !== 'pendiente') {
            session()->flash('error', 'Solo se pueden editar requisiciones en estado Pendiente.');
            return;
        }

        if ($requisicion->preparado_por_id !== Auth::id() && !$this->isManager()) {
            abort(403, 'No autorizado.');
        }

        $this->resetInput();
        $this->editingId = $requisicion->id;
        $this->editingNRequisicion = $requisicion->n_requisicion;
        $this->tipo = $requisicion->tipo;
        $this->proyecto = $requisicion->proyecto;
        $this->fecha = $requisicion->fecha->format('Y-m-d');
        $this->centro_costos = $requisicion->centro_costos;
        $this->unidad_solicitante = $requisicion->unidad_solicitante;
        $this->prioridad = $requisicion->prioridad;
        $this->observaciones = $requisicion->observaciones;

        $this->detalles = $requisicion->detalles->map(function ($detalle) {
            return [
                'cantidad' => (float) $detalle->cantidad,
                'unidad_medida' => $detalle->unidad_medida,
                'descripcion' => $detalle->descripcion,
                'certificado_calidad' => (bool) $detalle->certificado_calidad,
                'proveedor_sugerido' => $detalle->proveedor_sugerido,
                'observaciones' => $detalle->observaciones,
            ];
        })->toArray();

        $this->modalFormVisible = true;
    }

    public function update()
    {
        $this->authorize('edit requisicion');

        $requisicion = Requisicion::with('detalles')->findOrFail($this->editingId);

        if ($requisicion->estado !== 'pendiente') {
            session()->flash('error', 'Solo se pueden editar requisiciones en estado Pendiente.');
            return;
        }

        if ($requisicion->preparado_por_id !== Auth::id() && !$this->isManager()) {
            abort(403, 'No autorizado.');
        }

        $this->validate();

        try {
            DB::beginTransaction();

            $requisicion->update([
                'tipo' => $this->tipo,
                'proyecto' => $this->proyecto,
                'fecha' => $this->fecha,
                'centro_costos' => $this->centro_costos,
                'unidad_solicitante' => $this->unidad_solicitante,
                'prioridad' => $this->prioridad,
                'observaciones' => $this->observaciones,
            ]);

            // Reemplaza los detalles por la nueva versión del formulario
            $requisicion->detalles()->delete();
            foreach ($this->normalizarDetalles() as $detalle) {
                $requisicion->detalles()->create($detalle);
            }

            DB::commit();
            session()->flash('success', '¡Requisición ' . $requisicion->n_requisicion . ' actualizada con éxito!');
            $this->closeModal();

        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al actualizar la requisición: ' . $e->getMessage());
        }
    }

    // === DETALLE / CONSULTA ===

    public function openDetalleModal($id)
    {
        $this->authorize('show requisicion');

        $requisicion = Requisicion::with(['detalles.material', 'preparadoPor', 'verificadoPor', 'revisadoPor'])
            ->findOrFail($id);

        if ($requisicion->preparado_por_id !== Auth::id() && !$this->isManager()) {
            abort(403, 'No autorizado.');
        }

        $this->selectedId = $id;
        $this->modalDetalleVisible = true;
    }

    public function closeDetalleModal()
    {
        $this->modalDetalleVisible = false;
        $this->selectedId = null;
    }

    // === VERIFICACIÓN CONTRA INVENTARIO (ADMINISTRADOR) ===

    public function openVerificarModal($id)
    {
        $this->authorize('verify requisicion');

        $requisicion = Requisicion::with('detalles')->findOrFail($id);

        if (!in_array($requisicion->estado, ['pendiente', 'verificada'], true)) {
            session()->flash('error', 'Solo se pueden verificar requisiciones en estado Pendiente o Verificada.');
            return;
        }

        $this->materialesOptions = Material::orderBy('descripcion')
            ->get(['id', 'serial', 'descripcion', 'marca', 'cantidad', 'unidad_medida']);

        $this->verificacion = [];
        $this->sugerencias = [];

        foreach ($requisicion->detalles as $detalle) {
            $materialId = $detalle->material_id;
            $stockActual = null;

            if ($materialId) {
                $material = Material::find($materialId);
                $stockActual = $material ? (int) $material->cantidad : null;
            }

            $this->verificacion[] = [
                'id' => $detalle->id,
                'descripcion' => $detalle->descripcion,
                'cantidad' => (float) $detalle->cantidad,
                'unidad_medida' => $detalle->unidad_medida,
                'existe' => $detalle->existe_inventario,
                'material_id' => $materialId,
                'stock_actual' => $stockActual,
                'oc' => $detalle->oc,
                'observacion_verificacion' => $detalle->observacion_verificacion,
            ];

            $this->sugerencias[] = $this->sugerirMateriales($detalle->descripcion);
        }

        $this->selectedId = $id;
        $this->modalVerificarVisible = true;
    }

    public function closeVerificarModal()
    {
        $this->modalVerificarVisible = false;
        $this->selectedId = null;
        $this->verificacion = [];
        $this->sugerencias = [];
    }

    /**
     * Sugiere materiales del inventario cuya descripción comparte palabras
     * con la del renglón solicitado.
     */
    private function sugerirMateriales(string $descripcion): array
    {
        $palabras = preg_split('/[\s,;.\-\/()]+/', mb_strtolower(trim($descripcion)));
        $palabras = array_filter($palabras, fn ($p) => mb_strlen($p) >= 4);

        if (empty($palabras)) {
            return [];
        }

        return Material::query()
            ->where(function ($query) use ($palabras) {
                foreach ($palabras as $palabra) {
                    $query->orWhere('descripcion', 'like', '%' . $palabra . '%');
                }
            })
            ->limit(5)
            ->get(['id', 'serial', 'descripcion', 'marca', 'cantidad', 'unidad_medida'])
            ->toArray();
    }

    public function vincularMaterial($index, $materialId)
    {
        $material = Material::find($materialId);

        if (!$material || !isset($this->verificacion[$index])) {
            return;
        }

        $this->verificacion[$index]['material_id'] = $material->id;
        $this->verificacion[$index]['existe'] = true;
        $this->verificacion[$index]['stock_actual'] = (int) $material->cantidad;
    }

    public function desvincularMaterial($index)
    {
        if (!isset($this->verificacion[$index])) {
            return;
        }

        $this->verificacion[$index]['material_id'] = null;
        $this->verificacion[$index]['stock_actual'] = null;
    }

    public function saveVerificacion()
    {
        $this->authorize('verify requisicion');

        $requisicion = Requisicion::findOrFail($this->selectedId);

        if (!in_array($requisicion->estado, ['pendiente', 'verificada'], true)) {
            session()->flash('error', 'Solo se pueden verificar requisiciones en estado Pendiente o Verificada.');
            return;
        }

        try {
            DB::beginTransaction();

            foreach ($this->verificacion as $fila) {
                $detalle = DetalleRequisicion::find($fila['id']);

                if (!$detalle) {
                    continue;
                }

                $material = !empty($fila['material_id']) ? Material::find($fila['material_id']) : null;

                $detalle->update([
                    'existe_inventario' => (bool) ($fila['existe'] ?? false),
                    'material_id' => $material ? $material->id : null,
                    'stock_verificado' => $material ? (int) $material->cantidad : null,
                    'oc' => isset($fila['oc']) && trim((string) $fila['oc']) !== '' ? trim((string) $fila['oc']) : null,
                    'observacion_verificacion' => isset($fila['observacion_verificacion']) && trim((string) $fila['observacion_verificacion']) !== ''
                        ? trim((string) $fila['observacion_verificacion'])
                        : null,
                ]);
            }

            $requisicion->update([
                'estado' => 'verificada',
                'verificado_por_id' => Auth::id(),
                'verificada_at' => now(),
            ]);

            DB::commit();
            session()->flash('success', 'Requisición ' . $requisicion->n_requisicion . ' verificada contra el inventario.');
            $this->closeVerificarModal();

        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar la verificación: ' . $e->getMessage());
        }
    }

    // === CONTROL DE ENTREGA (ADMINISTRADOR / PROCURA) ===
    // Marca qué se ENTREGÓ y qué queda PENDIENTE de cada renglón
    // en las requisiciones verificadas o aprobadas.

    public function openEntregaModal($id)
    {
        $this->authorize('entregar requisicion');

        $requisicion = Requisicion::with('detalles')->findOrFail($id);

        if (!in_array($requisicion->estado, ['verificada', 'aprobada'], true)) {
            session()->flash('error', 'Solo se puede registrar la entrega de requisiciones en estado Verificada o Aprobada.');
            return;
        }

        $this->entregaItems = $requisicion->detalles->map(function ($detalle) {
            $cantidad = (float) $detalle->cantidad;
            $entregada = (float) ($detalle->cantidad_entregada ?? 0);

            return [
                'id' => $detalle->id,
                'item' => $detalle->item,
                'descripcion' => $detalle->descripcion,
                'cantidad' => $cantidad,
                'unidad_medida' => $detalle->unidad_medida,
                'cantidad_entregada' => $entregada,
                'entregado' => $entregada >= $cantidad && $cantidad > 0,
                'pendiente' => max(0, $cantidad - $entregada),
                'fecha' => $detalle->entregado_at?->format('d/m/Y H:i'),
            ];
        })->toArray();

        $this->selectedId = $id;
        $this->modalEntregaVisible = true;
    }

    public function closeEntregaModal()
    {
        $this->modalEntregaVisible = false;
        $this->selectedId = null;
        $this->entregaItems = [];
    }

    /**
     * Al escribir la cantidad entregada se recalcula automáticamente
     * lo que queda pendiente y si el renglón ya está completo.
     */
    public function updatedEntregaItems($value, $key)
    {
        if (!str_contains((string) $key, '.')) {
            return;
        }

        [$index, $campo] = explode('.', (string) $key);
        $index = (int) $index;

        if ($campo !== 'cantidad_entregada') {
            return;
        }

        if (!isset($this->entregaItems[$index])) {
            return;
        }

        $cantidad = (float) $this->entregaItems[$index]['cantidad'];
        $entregada = min(max(0, (float) $value), $cantidad);

        $this->entregaItems[$index]['cantidad_entregada'] = $entregada;
        $this->entregaItems[$index]['pendiente'] = max(0, $cantidad - $entregada);
        $this->entregaItems[$index]['entregado'] = $entregada >= $cantidad && $cantidad > 0;
        $this->entregaItems[$index]['fecha'] = $entregada > 0
            ? now()->format('d/m/Y H:i')
            : null;
    }

    public function saveEntrega()
    {
        $this->authorize('entregar requisicion');

        $requisicion = Requisicion::findOrFail($this->selectedId);

        if (!in_array($requisicion->estado, ['verificada', 'aprobada'], true)) {
            session()->flash('error', 'Solo se puede registrar la entrega de requisiciones en estado Verificada o Aprobada.');
            return;
        }

        try {
            DB::beginTransaction();

            foreach ($this->entregaItems as $fila) {
                $detalle = DetalleRequisicion::find($fila['id']);

                if (!$detalle) {
                    continue;
                }

                $cantidad = (float) $detalle->cantidad;
                $entregada = min(max(0, (float) ($fila['cantidad_entregada'] ?? 0)), $cantidad);
                $completo = $entregada >= $cantidad && $cantidad > 0;

                $detalle->update([
                    'cantidad_entregada' => $entregada,
                    'entregado' => $completo,
                    'entregado_at' => $entregada > 0 ? now() : null,
                    'entregado_por_id' => $entregada > 0 ? Auth::id() : null,
                ]);
            }

            DB::commit();

            $completos = 0;
            $totalPendiente = 0.0;
            foreach ($this->entregaItems as $fila) {
                $pendiente = max(0, (float) $fila['cantidad'] - (float) ($fila['cantidad_entregada'] ?? 0));
                if ($pendiente <= 0) {
                    $completos++;
                }
                $totalPendiente += $pendiente;
            }

            session()->flash(
                'success',
                'Entrega registrada para la requisición ' . $requisicion->n_requisicion
                . ': ' . $completos . ' de ' . count($this->entregaItems) . ' renglón(es) completo(s).'
                . ($totalPendiente > 0 ? ' Quedan pendientes ' . rtrim(rtrim(number_format($totalPendiente, 2, ',', '.'), '0'), ',') . ' unidades.' : '')
            );

            $this->closeEntregaModal();

        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar la entrega: ' . $e->getMessage());
        }
    }

    // === APROBACIÓN / RECHAZO (PLANIFICADOR) ===

    public function aprobar($id)
    {
        $this->authorize('approve requisicion');

        $requisicion = Requisicion::findOrFail($id);

        if (!in_array($requisicion->estado, ['pendiente', 'verificada'], true)) {
            session()->flash('error', 'Solo se pueden aprobar requisiciones en estado Pendiente o Verificada.');
            return;
        }

        $requisicion->update([
            'estado' => 'aprobada',
            'revisado_por_id' => Auth::id(),
            'aprobada_at' => now(),
            'motivo_rechazo' => null,
        ]);

        session()->flash('success', 'Requisición ' . $requisicion->n_requisicion . ' aprobada.');
    }

    public function openRechazarModal($id)
    {
        $this->authorize('reject requisicion');

        $requisicion = Requisicion::findOrFail($id);

        if (!in_array($requisicion->estado, ['pendiente', 'verificada'], true)) {
            session()->flash('error', 'Solo se pueden rechazar requisiciones en estado Pendiente o Verificada.');
            return;
        }

        $this->selectedId = $id;
        $this->motivoRechazo = '';
        $this->modalRechazoVisible = true;
    }

    public function rechazar()
    {
        $this->authorize('reject requisicion');

        $this->validate(['motivoRechazo' => 'required|string|max:2000']);

        $requisicion = Requisicion::findOrFail($this->selectedId);

        $requisicion->update([
            'estado' => 'rechazada',
            'revisado_por_id' => Auth::id(),
            'rechazada_at' => now(),
            'motivo_rechazo' => $this->motivoRechazo,
        ]);

        session()->flash('success', 'Requisición ' . $requisicion->n_requisicion . ' rechazada.');
        $this->modalRechazoVisible = false;
        $this->selectedId = null;
        $this->motivoRechazo = '';
    }

    public function closeRechazarModal()
    {
        $this->modalRechazoVisible = false;
        $this->selectedId = null;
        $this->motivoRechazo = '';
    }

    // === ELIMINACIÓN ===

    public function confirmDelete($id)
    {
        $this->authorize('delete requisicion');

        $requisicion = Requisicion::findOrFail($id);

        if (!in_array($requisicion->estado, ['pendiente', 'rechazada'], true)) {
            session()->flash('error', 'Solo se pueden eliminar requisiciones en estado Pendiente o Rechazada.');
            return;
        }

        $this->selectedId = $id;
        $this->modalConfirmDelete = true;
    }

    public function delete()
    {
        $this->authorize('delete requisicion');

        if ($this->selectedId) {
            $requisicion = Requisicion::find($this->selectedId);

            if ($requisicion) {
                $requisicion->delete();
                session()->flash('success', 'Requisición eliminada correctamente.');
            }

            $this->modalConfirmDelete = false;
            $this->selectedId = null;
        }
    }

    // === EXPORTAR PDF ===

    public function exportPdf($id)
    {
        $this->authorize('pdf requisicion');

        $requisicion = Requisicion::with(['detalles.material', 'preparadoPor', 'verificadoPor', 'revisadoPor'])
            ->findOrFail($id);

        $logoData = null;
        $logoPath = public_path($requisicion->tipo === 'sarcos_porras' ? 'logo_sarcos.jpg' : 'logo.jpg');
        if (file_exists($logoPath)) {
            $mime = pathinfo($logoPath, PATHINFO_EXTENSION) === 'png' ? 'image/png' : 'image/jpeg';
            $logoData = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $html = View::make('pdf.requisicion', [
            'requisicion' => $requisicion,
            'detalles' => $requisicion->detalles,
            'logoData' => $logoData,
        ])->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response()->streamDownload(
            fn () => print($dompdf->output()),
            'Requisicion_' . $requisicion->n_requisicion . '.pdf'
        );
    }

    // === BÚSQUEDA Y LISTADO ===

    public function searchRequisiciones()
    {
        $this->resetPage();
    }

    public function updatedFiltroEstado()
    {
        $this->resetPage();
    }

    public function updatedFiltroTipo()
    {
        $this->resetPage();
    }

    public function updatedFiltroPrioridad()
    {
        $this->resetPage();
    }

    private function resetInput()
    {
        $this->reset([
            'tipo', 'proyecto', 'fecha', 'centro_costos', 'unidad_solicitante',
            'prioridad', 'observaciones', 'detalles', 'editingId', 'editingNRequisicion',
        ]);

        $this->tipo = 'fabweld';
        $this->prioridad = 'intermedio';
        $this->fecha = now()->format('Y-m-d');
        $this->detalles = [];
        $this->addDetalle();
        $this->resetValidation();
    }

    public function render()
    {
        $isManager = $this->isManager();

        $requisiciones = Requisicion::query()
            ->with(['preparadoPor', 'verificadoPor', 'revisadoPor', 'detalles'])
            ->when(!$isManager, function ($query) {
                // Los usuarios normales solo ven sus propias requisiciones
                // en estado PENDIENTE o VERIFICADA.
                $query->where('preparado_por_id', Auth::id())
                    ->whereIn('estado', ['pendiente', 'verificada']);
            })
            ->when($this->search, function ($query) {
                $search = $this->search;
                $query->where(function ($q) use ($search) {
                    $q->where('n_requisicion', 'like', '%' . $search . '%')
                      ->orWhere('proyecto', 'like', '%' . $search . '%')
                      ->orWhere('centro_costos', 'like', '%' . $search . '%')
                      ->orWhere('unidad_solicitante', 'like', '%' . $search . '%')
                      ->orWhere('fecha', 'like', '%' . $search . '%')
                      ->orWhereHas('preparadoPor', function ($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%');
                      });
                });
            })
            ->when($this->filtroEstado, fn ($query) => $query->where('estado', $this->filtroEstado))
            ->when($this->filtroTipo, fn ($query) => $query->where('tipo', $this->filtroTipo))
            ->when($this->filtroPrioridad, fn ($query) => $query->where('prioridad', $this->filtroPrioridad))
            ->orderByDesc('created_at')
            ->paginate($this->paginate);

        return view('livewire.requisiciones.requisicion-index', [
            'requisiciones' => $requisiciones,
            'estadosFiltro' => $isManager
                ? $this->estados
                : array_intersect_key($this->estados, array_flip(['pendiente', 'verificada'])),
            'requisicionSeleccionada' => $this->selectedId
                ? Requisicion::with(['detalles.material', 'preparadoPor', 'verificadoPor', 'revisadoPor'])->find($this->selectedId)
                : null,
            'isManager' => $isManager,
        ]);
    }
}
