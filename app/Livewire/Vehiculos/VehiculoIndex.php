<?php

namespace App\Livewire\Vehiculos;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Vehiculo;
use Dompdf\Dompdf;
use Dompdf\Options;

class VehiculoIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $descripcion, $tipo, $marca, $observacion, $placa, $ano, $color, $bateria, $ubicacion;
    public $vehiculo_id;
    public $modalFormVisible = false;
    public $modalViewVisible = false;
    public $modalConfirmDelete = false;
    public $message = ''; // Propiedad para almacenar el mensaje de éxito

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        return [
            'descripcion' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'marca' => 'required|string|max:255',
            'observacion' => 'required|string|max:255',
            'placa' => 'required|string|max:255|unique:vehiculos,placa,' . $this->vehiculo_id,
            'ano' => 'required|integer|min:1900|max:' . date('Y'),
            'color' => 'required|string|max:255',
            'bateria' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
        ];
    }

    // Crear vehículo
    public function create()
    {
        $this->resetInput();
        $this->modalFormVisible = true;
    }

    // Guardar vehículo
    public function store()
    {
        $this->validate();
        Vehiculo::updateOrCreate(['id' => $this->vehiculo_id], $this->modelData());
        $this->modalFormVisible = false;
        $this->resetInput();

        // Mensaje de éxito
        $this->message = 'Vehículo guardado correctamente.';
    }

    // Editar vehículo
    public function edit($id)
    {
        $this->resetValidation();
        $this->vehiculo_id = $id;
        $v = Vehiculo::findOrFail($id);

        $this->descripcion = $v->descripcion;
        $this->tipo = $v->tipo;
        $this->marca = $v->marca;
        $this->observacion = $v->observacion;
        $this->placa = $v->placa;
        $this->ano = $v->ano;
        $this->color = $v->color;
        $this->bateria = $v->bateria;
        $this->ubicacion = $v->ubicacion;

        $this->modalFormVisible = true;
    }

    // Actualizar vehículo
    public function update()
    {
        $this->validate();
        Vehiculo::findOrFail($this->vehiculo_id)->update($this->modelData());
        $this->modalFormVisible = false;
        $this->resetInput();

        // Mensaje de éxito
        $this->message = 'Vehículo actualizado correctamente.';
    }

    // Ver vehículo
    public function view($id)
    {
        $v = Vehiculo::findOrFail($id);

        $this->descripcion = $v->descripcion;
        $this->tipo = $v->tipo;
        $this->marca = $v->marca;
        $this->observacion = $v->observacion;
        $this->placa = $v->placa;
        $this->ano = $v->ano;
        $this->color = $v->color;
        $this->bateria = $v->bateria;
        $this->ubicacion = $v->ubicacion;

        $this->modalViewVisible = true;
    }

    // Confirmar eliminación
    public function confirmDelete($id)
    {
        $this->vehiculo_id = $id;
        $this->modalConfirmDelete = true;
    }

    // Eliminar vehículo
    public function delete()
    {
        Vehiculo::destroy($this->vehiculo_id);
        $this->modalConfirmDelete = false;
        $this->resetInput();

        // Mensaje de éxito
        $this->message = 'Vehículo eliminado correctamente.';
    }

    // Exportar PDF
    public function exportPdf()
    {
        $query = Vehiculo::query();

        if ($this->search) {
            $query->where(function($q){
                $q->where('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('marca', 'like', '%' . $this->search . '%')
                  ->orWhere('placa', 'like', '%' . $this->search . '%');
            });
        }

        $vehiculos = $query->orderBy('id','desc')->get();

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        $html = view('pdf.vehiculos-list', compact('vehiculos'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, 'vehiculos.pdf');
    }

    // Datos para guardar/actualizar
    private function modelData()
    {
        return [
            'descripcion' => $this->descripcion,
            'tipo' => $this->tipo,
            'marca' => $this->marca,
            'observacion' => $this->observacion,
            'placa' => $this->placa,
            'ano' => $this->ano,
            'color' => $this->color,
            'bateria' => $this->bateria,
            'ubicacion' => $this->ubicacion,
        ];
    }

    // Reset inputs
    private function resetInput()
    {
        $this->reset(['descripcion','tipo','marca','observacion','placa','ano','color','bateria','ubicacion','vehiculo_id']);
        $this->resetValidation();
    }

    public function render()
    {
        $query = Vehiculo::query();

        if ($this->search) {
            $query->where(function($q){
                $q->where('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('marca', 'like', '%' . $this->search . '%')
                  ->orWhere('placa', 'like', '%' . $this->search . '%');
            });
        }

        $vehiculos = $query->orderBy('id','desc')->paginate(5);

        return view('livewire.vehiculos.vehiculo-index', [
            'vehiculos' => $vehiculos,
            'message' => $this->message, // Pasar el mensaje a la vista
        ]);
    }
}
