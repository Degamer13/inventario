<?php

namespace App\Livewire\MaquinariasFijas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MaquinariaFija;
use Dompdf\Dompdf;
use Dompdf\Options;

class MaquinariaFijaIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $descripcion, $modelo, $color, $marca, $serial, $codigo, $cantidad, $ubicacion;
    public $maquinaria_fija_id;
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
            'modelo'      => 'nullable|string|max:255',
            'color'       => 'nullable|string|max:255',
            'marca'       => 'nullable|string|max:255',
            'serial'      => 'nullable|string|max:255',
            'codigo'      => 'nullable|string|max:255',
            'cantidad'    => 'required|integer|min:0',
            'ubicacion'   => 'nullable|string|max:255',
        ];
    }

    // Crear
    public function create()
    {
        $this->resetInput();
        $this->modalFormVisible = true;
    }

    // Guardar
    public function store()
    {
        $this->validate();
        MaquinariaFija::updateOrCreate(['id' => $this->maquinaria_fija_id], $this->modelData());
        $this->modalFormVisible = false;
        $this->resetInput();
    }

    // Editar
    public function edit($id)
    {
        $this->resetValidation();
        $this->maquinaria_fija_id = $id;
        $m = MaquinariaFija::findOrFail($id);

        $this->descripcion = $m->descripcion;
        $this->modelo = $m->modelo;
        $this->color = $m->color;
        $this->marca = $m->marca;
        $this->serial = $m->serial;
        $this->codigo = $m->codigo;
        $this->cantidad = $m->cantidad;
        $this->ubicacion = $m->ubicacion;

        $this->modalFormVisible = true;
    }

    // Actualizar
    public function update()
    {
        $this->validate();
        MaquinariaFija::findOrFail($this->maquinaria_fija_id)->update($this->modelData());
        $this->modalFormVisible = false;
        $this->resetInput();
    }

    // Ver
    public function view($id)
    {
        $m = MaquinariaFija::findOrFail($id);

        $this->descripcion = $m->descripcion;
        $this->modelo = $m->modelo;
        $this->color = $m->color;
        $this->marca = $m->marca;
        $this->serial = $m->serial;
        $this->codigo = $m->codigo;
        $this->cantidad = $m->cantidad;
        $this->ubicacion = $m->ubicacion;

        $this->modalViewVisible = true;
    }

    // Confirmar eliminación
    public function confirmDelete($id)
    {
        $this->maquinaria_fija_id = $id;
        $this->modalConfirmDelete = true;
    }

    // Eliminar
    public function delete()
    {
        MaquinariaFija::destroy($this->maquinaria_fija_id);
        $this->modalConfirmDelete = false;
        $this->resetInput();
    }

    // Exportar PDF
    public function exportPdf()
    {
        $query = MaquinariaFija::query();

        if ($this->search) {
            $query->where(function($q){
                $q->where('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('marca', 'like', '%' . $this->search . '%')
                  ->orWhere('serial', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo', 'like', '%' . $this->search . '%')
                  ->orWhere('modelo', 'like', '%' . $this->search . '%');
            });
        }

        $maquinarias = $query->orderBy('id','desc')->get();

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        $html = view('pdf.maquinarias-fijas-list', compact('maquinarias'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, 'maquinarias_fijas.pdf');
    }

    // Datos del modelo
    private function modelData()
    {
        return [
            'descripcion' => $this->descripcion,
            'modelo'      => $this->modelo,
            'color'       => $this->color,
            'marca'       => $this->marca,
            'serial'      => $this->serial,
            'codigo'      => $this->codigo,
            'cantidad'    => $this->cantidad,
            'ubicacion'   => $this->ubicacion,
        ];
    }

    // Reset
    private function resetInput()
    {
        $this->reset([
            'descripcion','modelo','color','marca','serial','codigo','cantidad','ubicacion','maquinaria_fija_id'
        ]);
        $this->resetValidation();
    }

    public function render()
    {
        $query = MaquinariaFija::query();

        if ($this->search) {
            $query->where(function($q){
                $q->where('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('marca', 'like', '%' . $this->search . '%')
                  ->orWhere('serial', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo', 'like', '%' . $this->search . '%')
                  ->orWhere('modelo', 'like', '%' . $this->search . '%');
            });
        }

        $maquinarias = $query->orderBy('id','desc')->paginate(5);

        return view('livewire.maquinarias-fijas.maquinaria-fija-index', [
            'maquinarias' => $maquinarias
        ]);
    }
}
