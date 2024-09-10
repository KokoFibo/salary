<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\Karyawan;
use Livewire\WithPagination;

class Changeuserrolewr extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    public $id;
    public $username;
    public $tanggal_lahir;
    public $role;
    public $user_id;

    public function save()
    {
        $user = User::find($this->user_id);
        $user->role = $this->role;
        if ($this->role == 0) {
            $user->language = 'Cn';
        } else {
            $user->language = 'Id';
        }
        $user->save();
        // $this->dispatch('success', message: 'Role Karyawan berhasil di ganti');
        $this->dispatch(
            'message',
            type: 'success',
            title: 'ID: ' . $this->username . ' Role Karyawan berhasil di ganti',
        );
    }

    public function render()
    {
        if ($this->search) {
            $search = '%' . trim($this->search) . '%';
            $data = Karyawan::where('id_karyawan', $this->search)
                ->orWhere('nama', $this->search)
                ->first();
            if ($data != null) {
                $user = User::where('username', $data->id_karyawan)->first();
                $this->username = $data->id_karyawan;
                $this->tanggal_lahir = $data->tanggal_lahir;
            } else {
                $data = null;
                $this->id = null;
            }
        } else {
            $data = null;
            $this->id = null;
        }
        if ($data != null) {
            $user = User::where('username', $data->id_karyawan)->first();
            if ($user) {
                $this->role = $user->role;
                $this->user_id = $user->id;
            } else {
                $this->role = null;
            }
        }
        $dataRole = User::whereIn('role', [0, 2, 4, 5, 6, 7])
            ->whereNotIn('username', [50000, 60000, 70000])
            ->orderBy('role', 'asc')->paginate(10);

        return view('livewire.changeuserrolewr', compact(['data', 'dataRole']));
    }
}
