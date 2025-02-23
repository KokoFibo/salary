<?php

namespace App\Livewire;

use App\Models\Karyawan;
use App\Models\User;
use Livewire\Component;

class ChangeID extends Component
{
    public $idLama, $idBaru, $namaIdLama, $namaIdBaru;
    public $is_updateable_idBaru = false;
    public $is_updateable_idLama = false;
    public $isupdateable = false;
    public $idLamaError, $idBaruError, $idBarumessage;

    public function clear()
    {
        $this->isupdateable = false;
        $this->is_updateable_idBaru = false;
        $this->is_updateable_idLama = false;
        $this->idBaru = '';
        $this->idLama = '';
        $this->idLamaError = '';
        $this->idBaruError = '';
        $this->idBarumessage = '';
        $this->namaIdLama = '';
        $this->namaIdBaru = '';
    }


    public function rubah()
    {
        if ($this->isupdateable && $this->is_updateable_idBaru && $this->is_updateable_idLama) {
            try {
                $karyawan = Karyawan::where('id_karyawan', $this->idLama)->first();
                $karyawan->id_karyawan = $this->idBaru;
                $karyawan->save();
                $user = User::where('username', $this->idLama)->first();
                $user->username = $this->idBaru;
                $user->save();
                $this->clear();

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
    }

    public function cancel()
    {
        return redirect('/karyawanindex');
    }

    public function updatedIdLama()
    {
        $this->is_updateable_idLama = true;
        if ($this->idLama == '') {
            $this->is_updateable_idLama = false;
            return;
        }
        $data = Karyawan::where('id_karyawan', $this->idLama)->first();
        if (!$data) {
            $this->is_updateable_idLama = false;
            $this->idLamaError = 'ID Tidak Ditemukan';
            $this->isupdateable = false;
            return;
        } else {
            $this->is_updateable_idLama = true;
            $this->idLamaError = '';
            $this->namaIdLama = $data->nama;
        }
        $this->isupdateable = false;
        if ($this->is_updateable_idLama && $this->is_updateable_idBaru) {
            $this->isupdateable = true;
        }
    }
    public function updatedIdBaru()
    {
        $this->is_updateable_idBaru = true;
        $this->idBarumessage = '';

        if ($this->idBaru == '') {
            $this->is_updateable_idBaru = false;
            $this->isupdateable = false;

            return;
        }

        $data = Karyawan::where('id_karyawan', $this->idBaru)->first();
        if ($data) {
            $this->is_updateable_idBaru = false;
            $this->isupdateable = false;

            $this->idBaruError = 'ID ini sudah terdaftar';
            return;
        }
        $dataUser = User::where('username', $this->idBaru)->first();
        if ($data) {
            $this->is_updateable_idBaru = false;
            $this->idBaruError = 'ID ini sudah terdaftar pada database user';
            $this->isupdateable = false;

            return;
        }
        $this->idBaruError = '';
        $this->idBarumessage = 'ID ini Available, silakan dirubah';
        if ($this->is_updateable_idLama && $this->is_updateable_idBaru) {
            $this->isupdateable = true;
        }
    }
    public function render()
    {
        return view('livewire.change-i-d');
    }
}
