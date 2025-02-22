<?php

namespace App\Livewire;

use App\Models\Karyawan;
use App\Models\User;
use Livewire\Component;

class ChangeID extends Component
{
    public $idLama, $idBaru, $namaIdLama, $namaIdBaru;
    public $is_available = true;

    public function rubah()
    {
        if ($this->idBaru == '' || $this->idLama == '') $this->is_available = false;

        if ($this->is_available) {
            try {
                $karyawan = Karyawan::where('id_karyawan', $this->idLama)->first();
                $karyawan->id_karyawan = $this->idBaru;
                $karyawan->save();
                dd($karyawan->id_karyawan);
                $user = User::where('username', $this->idLama)->first();
                $user->username = $this->idBaru;
                $user->save();
                $this->dispatch(
                    'message',
                    type: 'success',
                    title: 'ID karyawan sudah berhasil di rubah',
                    position: 'center'
                );
            } catch (\Exception $e) {
                $this->dispatch(
                    'message',
                    type: 'error',
                    title: 'Terjadi kesalahan saat mengubah ID',
                    position: 'center'
                );
            }
        }
        // else {
        //     dd('tidak boleh ganti');
        // }
    }

    public function cancel()
    {
        return redirect('/karyawanindex');
    }

    public function updatedIdLama()
    {
        $data = Karyawan::where('id_karyawan', $this->idLama)->first();

        // Pastikan jika data ditemukan, gunakan nama dari database, jika tidak beri default.
        $this->namaIdLama = $data ? $data->nama : 'Nama Tidak Ditemukan';

        // Pastikan hanya mengakses property 'nama' jika data tidak null
        $this->is_available = $data ? false : true;
    }
    public function updatedIdBaru()
    {
        $data = Karyawan::where('id_karyawan', $this->idBaru)->first();
        $this->namaIdBaru = $data ? $data->nama : '';
        $this->is_available = $data ? false : true;
    }
    public function render()
    {
        return view('livewire.change-i-d');
    }
}
