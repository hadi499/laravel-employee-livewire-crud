<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee as ModelEmployee;
use Livewire\WithPagination;

class Employee extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $nama;
    public $email;
    public $alamat;
    public $updateData = false;
    public $employee_id;
    public $katakunci;
    public $employee_selected_id = [];



    public function store()
    {
        $rules = [
            'nama' => 'required',
            'email' => 'required|email',
            'alamat' => 'required'
        ];

        $pesan_error = [
            'nama.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak sesuai',
            'alamat.required' => 'Alamat wajib diisi'
        ];
        $validated = $this->validate($rules, $pesan_error);
        ModelEmployee::create($validated);
        session()->flash('message', 'Data berhasil dibuat.');
        $this->clear();
    }

    public function edit($id)
    {
        $data = ModelEmployee::find($id);
        $this->nama = $data->nama;
        $this->email = $data->email;
        $this->alamat = $data->alamat;
        $this->updateData = true;
        $this->employee_id = $id;
       
    }

    public function update()
    {
        $rules = [
            'nama' => 'required',
            'email' => 'required|email',
            'alamat' => 'required'
        ];

        $pesan_error = [
            'nama.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak sesuai',
            'alamat.required' => 'Alamat wajib diisi'
        ];
        $validated = $this->validate($rules, $pesan_error);
        $data = ModelEmployee::find($this->employee_id);

        $data->update($validated);
        session()->flash('message', 'Data berhasil diupdate.');
        $this->clear();
    }

    public function clear()
    {
        $this->nama = '';
        $this->email = '';
        $this->alamat = '';
        $this->updateData = false;
        $this->employee_id = '';
        $this->employee_selected_id = [];
    }

    public function delete()
    {
        if ($this->employee_id != '') {
            $id = $this->employee_id;
            ModelEmployee::find($id)->delete();
        }
        if (count($this->employee_selected_id)) {
            for ($x = 0; $x < count($this->employee_selected_id); $x++) {
                ModelEmployee::find($this->employee_selected_id[$x])->delete();
            }
        }
        session()->flash('message', 'Data berhasil dihapus.');
        $this->clear();
    }

    public function delete_confirmation($id)
    {
        if($id != '') {
            $this->employee_id = $id;
        }
    }

    public function render()
    {
        if ($this->katakunci != null) {
            $data = ModelEmployee::where('nama', 'like', '%' . $this->katakunci . '%')
                ->orWhere('email', 'like', '%' . $this->katakunci . '%')
                ->orWhere('alamat', 'like', '%' . $this->katakunci . '%')
                ->orderBy('nama', 'asc')->paginate(2);
        } else {

            $data = ModelEmployee::orderBy('nama', 'asc')->paginate(2);
        }

        return view('livewire.employee', ['dataEmployees' => $data]);
    }
}
