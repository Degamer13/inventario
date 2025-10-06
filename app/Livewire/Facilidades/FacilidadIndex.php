<?php

namespace App\Livewire\Facilidades;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Facilidad;
use Dompdf\Dompdf;
use Dompdf\Options;

class FacilidadIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $descripcion, $serial, $marca, $cantidad, $ubicacion;
    public $facilidad_id;
    public $modalFormVisible = false;
    public $modalViewVisible = false;
    public $modalConfirmDelete = false;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        return [
            'descripcion' => 'required|string|max:255',
            'serial' => 'nullable|string|max:255',
            'marca' => 'nullable|string|max:255',
            'cantidad' => 'required|integer|min:0',
            'ubicacion' => 'nullable|string|max:255',
        ];
    }

    // Crear facilidad
    public function create()
    {
        $this->resetInput();
        $this->modalFormVisible = true;
    }

    // Guardar facilidad
    public function store()
    {
        $this->validate();
        Facilidad::updateOrCreate(['id' => $this->facilidad_id], $this->modelData());
        $this->modalFormVisible = false;
        $this->resetInput();
    }

    // Editar facilidad
    public function edit($id)
    {
        $this->resetValidation();
        $this->facilidad_id = $id;
        $f = Facilidad::findOrFail($id);

        $this->descripcion = $f->descripcion;
        $this->serial = $f->serial;
        $this->marca = $f->marca;
        $this->cantidad = $f->cantidad;
        $this->ubicacion = $f->ubicacion;

        $this->modalFormVisible = true;
    }

    // Actualizar facilidad
    public function update()
    {
        $this->validate();
        Facilidad::findOrFail($this->facilidad_id)->update($this->modelData());
        $this->modalFormVisible = false;
        $this->resetInput();
    }

    // Ver facilidad
    public function view($id)
    {
        $f = Facilidad::findOrFail($id);

        $this->descripcion = $f->descripcion;
        $this->serial = $f->serial;
        $this->marca = $f->marca;
        $this->cantidad = $f->cantidad;
        $this->ubicacion = $f->ubicacion;

        $this->modalViewVisible = true;
    }

    // Confirmar eliminación
    public function confirmDelete($id)
    {
        $this->facilidad_id = $id;
        $this->modalConfirmDelete = true;
    }

    // Eliminar facilidad
    public function delete()
    {
        Facilidad::destroy($this->facilidad_id);
        $this->modalConfirmDelete = false;
        $this->resetInput();
    }

    // Exportar PDF
    public function exportPdf()
    {
        $query = Facilidad::query();

        if ($this->search) {
            $query->where(function($q){
                $q->where('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('marca', 'like', '%' . $this->search . '%')
                  ->orWhere('serial', 'like', '%' . $this->search . '%');
            });
        }

        $facilidades = $query->orderBy('id','desc')->get();

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        $html = view('pdf.facilidades-list', compact('facilidades'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, 'facilidades.pdf');
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
        $this->reset(['descripcion','serial','marca','cantidad','ubicacion','facilidad_id']);
        $this->resetValidation();
    }

    public function render()
    {
        $query = Facilidad::query();

        if ($this->search) {
            $query->where(function($q){
                $q->where('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('marca', 'like', '%' . $this->search . '%')
                  ->orWhere('serial', 'like', '%' . $this->search . '%');
            });
        }

        $facilidades = $query->orderBy('id','desc')->paginate(5);

        return view('livewire.facilidades.facilidad-index', [
            'facilidades' => $facilidades
        ]);
    }
}
