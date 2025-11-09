<?php

namespace App\Livewire\Sistemas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sistema;
use Dompdf\Dompdf;
use Dompdf\Options;

class SistemaIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $descripcion, $serial, $marca, $cantidad, $ubicacion;
    public $sistema_id;
    public $modalFormVisible = false;
    public $modalViewVisible = false;
    public $modalConfirmDelete = false;
    public $message = ''; // Para almacenar el mensaje de éxito

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        return [
            'descripcion' => 'required|string|max:255',
            'serial' => 'required|string|max:255|unique:sistemas,serial,' . $this->sistema_id,
            'marca' => 'required|string|max:255',
            'cantidad' => 'required|integer|min:0',
            'ubicacion' => 'required|string|max:255',
        ];
    }

    // Crear sistema
    public function create()
    {
        $this->resetInput();
        $this->modalFormVisible = true;
    }

    // Guardar sistema
    public function store()
    {
        $this->validate();
        Sistema::updateOrCreate(['id' => $this->sistema_id], $this->modelData());
        $this->modalFormVisible = false;
        $this->resetInput();

        // Mensaje de éxito
        $this->message = 'Sistema guardado correctamente.';
    }

    // Editar sistema
    public function edit($id)
    {
        $this->resetValidation();
        $this->sistema_id = $id;
        $s = Sistema::findOrFail($id);

        $this->descripcion = $s->descripcion;
        $this->serial = $s->serial;
        $this->marca = $s->marca;
        $this->cantidad = $s->cantidad;
        $this->ubicacion = $s->ubicacion;

        $this->modalFormVisible = true;
    }

    // Actualizar sistema
    public function update()
    {
        $this->validate();
        Sistema::findOrFail($this->sistema_id)->update($this->modelData());
        $this->modalFormVisible = false;
        $this->resetInput();

        // Mensaje de éxito
        $this->message = 'Sistema actualizado correctamente.';
    }

    // Ver sistema
    public function view($id)
    {
        $s = Sistema::findOrFail($id);

        $this->descripcion = $s->descripcion;
        $this->serial = $s->serial;
        $this->marca = $s->marca;
        $this->cantidad = $s->cantidad;
        $this->ubicacion = $s->ubicacion;

        $this->modalViewVisible = true;
    }

    // Confirmar eliminación
    public function confirmDelete($id)
    {
        $this->sistema_id = $id;
        $this->modalConfirmDelete = true;
    }

    // Eliminar sistema
    public function delete()
    {
        Sistema::destroy($this->sistema_id);
        $this->modalConfirmDelete = false;
        $this->resetInput();

        // Mensaje de éxito
        $this->message = 'Sistema eliminado correctamente.';
    }

    // Exportar PDF
    public function exportPdf()
    {
        $query = Sistema::query();

        if ($this->search) {
            $query->where(function($q){
                $q->where('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('marca', 'like', '%' . $this->search . '%')
                  ->orWhere('serial', 'like', '%' . $this->search . '%');
            });
        }

        $sistemas = $query->orderBy('id','desc')->get();

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        $html = view('pdf.sistemas-list', compact('sistemas'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, 'sistemas.pdf');
    }

    // Datos para guardar/actualizar
    private function modelData()
    {
        return [
            'descripcion' => $this->descripcion,
            'serial' => $this->serial,
            'marca' => $this->marca,
            'cantidad' => $this->cantidad,
            'ubicacion' => $this->ubicacion,
        ];
    }

    // Reset inputs
    private function resetInput()
    {
        $this->reset(['descripcion','serial','marca','cantidad','ubicacion','sistema_id']);
        $this->resetValidation();
    }

    public function render()
    {
        $query = Sistema::query();

        if ($this->search) {
            $query->where(function($q){
                $q->where('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('marca', 'like', '%' . $this->search . '%')
                  ->orWhere('serial', 'like', '%' . $this->search . '%');
            });
        }

        $sistemas = $query->orderBy('id','desc')->paginate(5);

        return view('livewire.sistemas.sistema-index', [
            'sistemas' => $sistemas,
            'message' => $this->message, // Pasar el mensaje a la vista
        ]);
    }
}
