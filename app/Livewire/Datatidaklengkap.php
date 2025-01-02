<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Karyawan;

class Datatidaklengkap extends Component
{


    public function render()
    {
        $statusKaryawan = ['PKWT', 'PKWTT', 'Dirumahkan'];

        $metode_penggajian = Karyawan::where('metode_penggajian', '')
            ->whereIn('status_karyawan', $statusKaryawan)
            ->get();


        $company = Karyawan::where('company_id', 100)
            ->whereIn('status_karyawan', $statusKaryawan)
            ->get();

        $placement = Karyawan::where('placement_id', 100)
            ->whereIn('status_karyawan', $statusKaryawan)
            ->get();

        $jabatan = Karyawan::where('jabatan_id', 100)
            ->whereIn('status_karyawan', $statusKaryawan)
            ->get();

        return view('livewire.datatidaklengkap', [
            'metode_penggajian' => $metode_penggajian,
            'gaji_pokok' => $gaji_pokok,
            'company' => $company,
            'placement' => $placement,
            'jabatan' => $jabatan,
        ]);
    }
}
