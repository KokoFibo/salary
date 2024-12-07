<?php

namespace App\Livewire;

use App\Models\Karyawan;
use App\Models\Yfrekappresensi;
use Livewire\Component;

class Infokaryawan extends Component
{

    public function render()
    {
        $total_karyawan_aktif = Karyawan::whereNotIn('status_karyawan', ['Resigned', 'Blacklist'])->count();
        $total_karyawan_hadir_hari_ini = Yfrekappresensi::where('date', today())->count();


        $jumlahTanpaRekening = Karyawan::where('nomor_rekening', '')->whereNotIn('status_karyawan', ['Resigned', 'Blacklist'])->count();
        $dataTanpaRekening = Karyawan::where('nomor_rekening', '')->whereNotIn('status_karyawan', ['Resigned', 'Blacklist'])->get();
        $pkwt = Karyawan::where('status_karyawan', 'PKWT')->count();
        $pkwtt = Karyawan::where('status_karyawan', 'PKWTT')->count();
        $dirumahkan = Karyawan::where('status_karyawan', 'Dirumahkan')->count();
        $resigned = Karyawan::where('status_karyawan', 'Resigned')->count();
        $blacklist = Karyawan::where('status_karyawan', 'Blacklist')->count();
        return view('livewire.infokaryawan', [
            'total_karyawan_aktif' => $total_karyawan_aktif,
            'total_karyawan_hadir_hari_ini' => $total_karyawan_hadir_hari_ini,
            'jumlahTanpaRekening' => $jumlahTanpaRekening,
            'dataTanpaRekening' => $dataTanpaRekening,
            'pkwt' => $pkwt,
            'pkwtt' => $pkwtt,
            'dirumahkan' => $dirumahkan,
            'resigned' => $resigned,
            'blacklist' => $blacklist,
        ]);
    }
}
