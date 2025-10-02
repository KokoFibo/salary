<?php

namespace App\Livewire;

use App\Models\Karyawan;
use App\Models\Yfrekappresensi;
use App\Models\Applicantfile;

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
        $data = Karyawan::whereNotNull('id_file_karyawan')->whereIn('status_karyawan', ['PKWT', 'PKWTT', 'Dirumahkan'])->get();
        $karyawan_gaji_0_count = Karyawan::where('gaji_tetap', 0)->whereNotIn('status_karyawan', ['Resigned', 'Blacklist'])->count();
        $karyawan_gaji_0 = Karyawan::where('gaji_tetap', 0)->whereNotIn('status_karyawan', ['Resigned', 'Blacklist'])->get();

        $karyawan_berdokumen = 0;
        foreach ($data as $d) {
            $applicanteFiles = Applicantfile::where('id_karyawan', $d->id_file_karyawan)->count();
            if ($applicanteFiles > 0) $karyawan_berdokumen++;
        }
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
            'karyawan_berdokumen' => $karyawan_berdokumen,
            'karyawan_gaji_0' => $karyawan_gaji_0,
            'karyawan_gaji_0_count' => $karyawan_gaji_0_count,

        ]);
    }
}
