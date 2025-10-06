<?php

namespace App\Livewire\Materiales;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Material;
use Dompdf\Dompdf;
use Dompdf\Options;

class MaterialIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $descripcion, $serial, $marca, $cantidad, $unidad_medida, $ubicacion;
    public $material_id;
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
            'unidad_medida' => 'nullable|string|max:255',
            'ubicacion' => 'nullable|string|max:255',
        ];
    }

    public function create()
    {
        $this->resetInput();
        $this->modalFormVisible = true;
    }

    public function store()
    {
        $this->validate();
        Material::updateOrCreate(['id' => $this->material_id], $this->modelData());
        $this->modalFormVisible = false;
        $this->resetInput();
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->material_id = $id;
        $m = Material::findOrFail($id);

        $this->descripcion = $m->descripcion;
        $this->serial = $m->serial;
        $this->marca = $m->marca;
        $this->cantidad = $m->cantidad;
        $this->unidad_medida = $m->unidad_medida;
        $this->ubicacion = $m->ubicacion;

        $this->modalFormVisible = true;
    }

    public function update()
    {
        $this->validate();
        Material::findOrFail($this->material_id)->update($this->modelData());
        $this->modalFormVisible = false;
        $this->resetInput();
    }

    public function view($id)
    {
        $m = Material::findOrFail($id);

        $this->descripcion = $m->descripcion;
        $this->serial = $m->serial;
        $this->marca = $m->marca;
        $this->cantidad = $m->cantidad;
        $this->unidad_medida = $m->unidad_medida;
        $this->ubicacion = $m->ubicacion;

        $this->modalViewVisible = true;
    }

    public function confirmDelete($id)
    {
        $this->material_id = $id;
        $this->modalConfirmDelete = true;
    }

    public function delete()
    {
        Material::destroy($this->material_id);
        $this->modalConfirmDelete = false;
        $this->resetInput();
    }

    // Exportar PDF
    public function exportPdf()
    {
        $query = Material::query();

        if ($this->search) {
            $query->where(function($q){
                $q->where('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('marca', 'like', '%' . $this->search . '%')
                  ->orWhere('serial', 'like', '%' . $this->search . '%');
            });
        }

        $materiales = $query->orderBy('id','desc')->get();

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        $html = view('pdf.materiales-list', compact('materiales'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, 'materiales.pdf');
    }

    private function modelData()
    {
        return [
            'descripcion' => $this->descripcion,
            'serial' => $this->serial,
            'marca' => $this->marca,
            'cantidad' => $this->cantidad,
            'unidad_medida' => $this->unidad_medida,
            'ubicacion' => $this->ubicacion,
        ];
    }

    private function resetInput()
    {
        $this->reset(['descripcion','serial','marca','cantidad','unidad_medida','ubicacion','material_id']);
        $this->resetValidation();
    }

    public function render()
    {
        $query = Material::query();

        if ($this->search) {
            $query->where(function($q){
                $q->where('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('marca', 'like', '%' . $this->search . '%')
                  ->orWhere('serial', 'like', '%' . $this->search . '%');
            });
        }

        $materiales = $query->orderBy('id','desc')->paginate(5);

        return view('livewire.materiales.material-index', [
            'materiales' => $materiales
        ]);
    }
}
