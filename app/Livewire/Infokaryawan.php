<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Karyawan;
use App\Models\Applicantfile;

use App\Models\Yfrekappresensi;
use Illuminate\Support\Facades\DB;

class Infokaryawan extends Component
{
    public $month, $year;
    public $showKaryawanTanpaEmail = false;
    public $showKaryawanTerlambat = false;
    public $selectBulan;

    public function updatedSelectBulan()
    {
        if ($this->selectBulan == 0) {
            // Bulan sekarang
            $this->month = now()->month;
            $this->year  = now()->year;
        } else {
            // Bulan lalu
            $this->month = now()->subMonth()->month;
            $this->year  = now()->subMonth()->year;
        }
    }

    public function toggleKaryawanTerlambat()
    {
        $this->showKaryawanTerlambat = !$this->showKaryawanTerlambat;
    }
    public function toggleKaryawanTanpaEmail()
    {
        $this->showKaryawanTanpaEmail = !$this->showKaryawanTanpaEmail;
    }

    public function mount()
    {
        $this->month = now()->month; // bulan sekarang (1–12)
        $this->year  = now()->year;  // tahun sekarang (misal 2025)
        $this->selectBulan = 0; // bulan ini
    }
    public function render()
    {
        $total_karyawan_aktif = Karyawan::whereNotIn('status_karyawan', ['Resigned', 'Blacklist'])->count();

        // $total_karyawan_hadir_hari_ini = Yfrekappresensi::whereDate('date', today())->count();

        $jumlahTanpaRekening = Karyawan::where('nomor_rekening', '')
            ->whereNotIn('status_karyawan', ['Resigned', 'Blacklist'])
            ->count();

        $dataTanpaRekening = Karyawan::where('nomor_rekening', '')
            ->whereNotIn('status_karyawan', ['Resigned', 'Blacklist'])
            ->get();

        $total_karyawan_perbulan = Karyawan::where('metode_penggajian', 'Perbulan')
            ->whereNotIn('status_karyawan', ['Resigned', 'Blacklist'])->count();

        $total_karyawan_perjam = Karyawan::where('metode_penggajian', 'Perjam')
            ->whereNotIn('status_karyawan', ['Resigned', 'Blacklist'])->count();

        // hitung semua status dalam 1 query
        $statusCount = Karyawan::select('status_karyawan', DB::raw('COUNT(*) as total'))
            ->whereIn('status_karyawan', ['PKWT', 'PKWTT', 'Dirumahkan', 'Resigned', 'Blacklist'])
            ->groupBy('status_karyawan')
            ->pluck('total', 'status_karyawan');

        // hitung jumlah karyawan yg punya ApplicantFile dalam 1 query bukan looping
        $karyawan_berdokumen = Applicantfile::distinct('id_karyawan')->count('id_karyawan');

        // 1. Ambil semua karyawan aktif yang seharusnya punya dokumen
        $karyawanYgHarusPunyaDokumen = Karyawan::whereNotNull('id_file_karyawan')
            ->whereIn('status_karyawan', ['PKWT', 'PKWTT', 'Dirumahkan'])
            ->pluck('id_file_karyawan')
            ->toArray();

        // 2. Ambil semua id_karyawan yang sudah punya dokumen
        $karyawanYgSudahPunyaDokumen = Applicantfile::distinct('id_karyawan')
            ->pluck('id_karyawan')
            ->toArray();

        // 3. Cari yang belum punya dokumen
        $karyawan_tanpa_dokumen = array_diff(
            $karyawanYgHarusPunyaDokumen,
            $karyawanYgSudahPunyaDokumen
        );

        // 4. Hitung jumlahnya
        $jumlah_karyawan_tanpa_dokumen = count($karyawan_tanpa_dokumen);

        $karyawan_perbulan_tanpa_ptkp = Karyawan::whereNotNull('id_file_karyawan')
            ->whereIn('status_karyawan', ['PKWT', 'PKWTT', 'Dirumahkan'])
            ->where('ptkp', null)->count();

        $karyawan_tanpa_email = Karyawan::whereNotNull('id_file_karyawan')
            ->whereIn('status_karyawan', ['PKWT', 'PKWTT', 'Dirumahkan'])
            ->where(function ($q) {
                $q->whereNull('email')
                    ->orWhere('email', '')
                    ->orWhere('email', ' ');
            })
            ->get();

        $jumlah_karyawan_tanpa_email = $karyawan_tanpa_email->count();

        $karyawan_telat = Yfrekappresensi::join(
            'karyawans',
            'karyawans.id_karyawan',
            '=',
            'yfrekappresensis.user_id'
        )
            ->whereYear('date', $this->year)
            ->whereMonth('date', $this->month)
            ->whereIn('karyawans.status_karyawan', ['PKWT', 'PKWTT', 'Dirumahkan'])
            ->where('karyawans.metode_penggajian', 'Perbulan')
            ->where('yfrekappresensis.late', '>', 0)
            ->select(
                'yfrekappresensis.user_id',
                'karyawans.nama',
                'karyawans.jabatan_id',
                'karyawans.status_karyawan',
                'karyawans.company_id'
            )
            ->selectRaw('COUNT(*) as total_terlambat')
            ->groupBy(
                'yfrekappresensis.user_id',
                'karyawans.nama',
                'karyawans.jabatan_id',
                'karyawans.status_karyawan',
                'karyawans.company_id'
            )
            ->orderBy('total_terlambat', 'desc')
            ->get();

        $total_karyawan_telat = $karyawan_telat->count();

        return view('livewire.infokaryawan', [
            'total_karyawan_aktif' => $total_karyawan_aktif,
            // 'total_karyawan_hadir_hari_ini' => $total_karyawan_hadir_hari_ini,
            'jumlahTanpaRekening' => $jumlahTanpaRekening,
            'dataTanpaRekening' => $dataTanpaRekening,

            'pkwt'       => $statusCount['PKWT']       ?? 0,
            'pkwtt'      => $statusCount['PKWTT']      ?? 0,
            'dirumahkan' => $statusCount['Dirumahkan'] ?? 0,
            'resigned'   => $statusCount['Resigned']   ?? 0,
            'blacklist'  => $statusCount['Blacklist']  ?? 0,

            'karyawan_berdokumen' => $karyawan_berdokumen,
            'total_karyawan_perjam' => $total_karyawan_perjam,
            'total_karyawan_perbulan' => $total_karyawan_perbulan,
            'jumlah_karyawan_tanpa_dokumen' => $jumlah_karyawan_tanpa_dokumen,
            'karyawan_perbulan_tanpa_ptkp' => $karyawan_perbulan_tanpa_ptkp,
            'karyawan_tanpa_email' => $karyawan_tanpa_email,
            'jumlah_karyawan_tanpa_email' => $jumlah_karyawan_tanpa_email,
            'karyawan_telat' => $karyawan_telat,
            'total_karyawan_telat' => $total_karyawan_telat,
        ]);
    }
}
