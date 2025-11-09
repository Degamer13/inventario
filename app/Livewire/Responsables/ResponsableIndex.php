<?php

namespace App\Livewire\Responsables;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Responsable;
use Dompdf\Dompdf;
use Dompdf\Options;

class ResponsableIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $name, $cedula, $email, $telefono, $cargo, $area;
    public $responsable_id;
    public $modalFormVisible = false;
    public $modalViewVisible = false;
    public $modalConfirmDelete = false;

    protected $paginationTheme = 'tailwind';

    // Resetear la paginación al actualizar la búsqueda
    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:responsables,name,' . $this->responsable_id,
            'cedula' => 'required|string|max:20|unique:responsables,cedula,' . $this->responsable_id,
            'email' => 'nullable|email|unique:responsables,email,' . $this->responsable_id,
            'telefono' => 'nullable|string|max:50|unique:responsables,telefono,' . $this->responsable_id,
            'cargo' => 'nullable|string|max:255',
            'area' => 'nullable|string|max:255',
        ];
    }

    public function mount()
    {
        if (!auth()->user()->can('list responsable')) {
            abort(403);
        }
    }

    // Crear responsable
    public function create()
    {
        if (!auth()->user()->can('create responsable')) abort(403);
        $this->resetInput();
        $this->modalFormVisible = true;
    }

    // Guardar responsable
    public function store()
    {
        if (!auth()->user()->can('create responsable')) abort(403);
        $this->validate();

        Responsable::create($this->modelData());
        $this->modalFormVisible = false;
        $this->resetInput();

        session()->flash('message', 'Responsable creado correctamente.');
    }

    // Editar responsable
    public function edit($id)
    {
        if (!auth()->user()->can('edit responsable')) abort(403);
        $this->resetValidation();
        $this->responsable_id = $id;
        $r = Responsable::findOrFail($id);

        $this->name = $r->name;
        $this->cedula = $r->cedula;
        $this->email = $r->email;
        $this->telefono = $r->telefono;
        $this->cargo = $r->cargo;
        $this->area = $r->area;

        $this->modalFormVisible = true;
    }

    // Actualizar responsable
    public function update()
    {
        if (!auth()->user()->can('edit responsable')) abort(403);
        $this->validate();

        Responsable::findOrFail($this->responsable_id)->update($this->modelData());
        $this->modalFormVisible = false;
        $this->resetInput();

        session()->flash('message', 'Responsable actualizado correctamente.');
    }

    // Ver responsable
    public function view($id)
    {
        if (!auth()->user()->can('show responsable')) abort(403);
        $r = Responsable::findOrFail($id);

        $this->name = $r->name;
        $this->cedula = $r->cedula;
        $this->email = $r->email;
        $this->telefono = $r->telefono;
        $this->cargo = $r->cargo;
        $this->area = $r->area;

        $this->modalViewVisible = true;
    }

    // Confirmar eliminación
    public function confirmDelete($id)
    {
        if (!auth()->user()->can('delete responsable')) abort(403);
        $this->responsable_id = $id;
        $this->modalConfirmDelete = true;
    }

    // Eliminar responsable
    public function delete()
    {
        if (!auth()->user()->can('delete responsable')) abort(403);
        Responsable::destroy($this->responsable_id);
        $this->modalConfirmDelete = false;
        $this->resetInput();

        session()->flash('message', 'Responsable eliminado correctamente.');
    }

    // Método para exportar a PDF
    public function exportPdf()
    {
        if (!auth()->user()->can('pdf responsable')) abort(403);

        $query = Responsable::query();
        if ($this->search) {
            $query->where(function($q){
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('cedula', 'like', '%' . $this->search . '%');
            });
        }
        $responsables = $query->orderBy('id','desc')->get();

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        $html = view('pdf.responsables-list', compact('responsables'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, 'responsables.pdf');
    }

    // Datos para guardar/actualizar
    private function modelData()
    {
        return [
            'name' => $this->name,
            'cedula' => $this->cedula,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'cargo' => $this->cargo,
            'area' => $this->area,
        ];
    }

    // Reset inputs
    private function resetInput()
    {
        $this->reset(['name', 'cedula', 'email', 'telefono', 'cargo', 'area', 'responsable_id']);
        $this->resetValidation();
    }

    // Renderizar
    public function render()
    {
        $query = Responsable::query();

        if ($this->search) {
            $query->where(function($q){
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('cedula', 'like', '%' . $this->search . '%');
            });
        }

        $responsables = $query->orderBy('id','desc')->paginate(5);

        return view('livewire.responsables.responsable-index', [
            'responsables' => $responsables
        ]);
    }
}
