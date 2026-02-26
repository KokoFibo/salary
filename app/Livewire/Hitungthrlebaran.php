<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Karyawan;
use App\Models\Bonuspotongan;

use Livewire\WithPagination;
use App\Exports\THRLebaranExport;
use Maatwebsite\Excel\Facades\Excel;

class Hitungthrlebaran extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $cutOffDate;
    public $cutoffMinus30;

    public function pindahKeBonusPotongan()
    {
        $karyawans = Karyawan::whereNotIn('etnis', ['China', 'Tionghoa'])
            ->whereIn('status_karyawan', ['PKWT', 'PKWTT'])
            ->where('tanggal_bergabung', '<', $this->cutoffMinus30)
            ->get();
        foreach ($karyawans as $karyawan) {

            $thr = $this->hitungTHR(
                $karyawan->id_karyawan,
                $karyawan->tanggal_bergabung,
                $karyawan->gaji_pokok,
                $this->cutOffDate

            );


            $record = Bonuspotongan::where('user_id', $karyawan->id_karyawan)
                ->whereYear('tanggal', 2026)
                ->whereMonth('tanggal', 2)
                ->first();
            if ($record) {
                // dd($record);
                $record->update([
                    'thr' => $thr
                ]);
            } else {
                Bonuspotongan::create([
                    'karyawan_id' => $karyawan->id,
                    'user_id'     => $karyawan->id_karyawan,
                    'tanggal'     => Carbon::create(2026, 2, 26),
                    'thr'         => $thr
                ]);
            }
        }

        $this->dispatch(
            'message',
            type: 'success',
            title: 'THR berhasil dipindahkan ke Bonus & Potongan',
        );
    }

    public function mount()
    {
        $this->cutOffDate = '2026-03-21';
        $this->cutoffMinus30 = Carbon::parse($this->cutOffDate)->subDays(30);
    }

    public function hitungTHR($id, $tgl, $gaji, $tanggal_akhir)
    {
        $thr = 0;

        $lama_kerja = selisihBulanBulat($tgl, $tanggal_akhir);
        if ($lama_kerja < 12) {
            return $gaji / 12 * $lama_kerja;
        } else {
            return $gaji;
        }
        // $selisih_hari = selisihHari($tgl, $tanggal_akhir);
        // if ($selisih_hari > 365) {
        //     $thr = $gaji;
        // } else {
        //     $karyawan = Karyawan::where('id_karyawan', $id)->first();
        //     // $selisih_hari = Carbon::parse($tgl)->diffInDays(Carbon::parse($tanggal_akhir));
        //     $thr =  $selisih_hari / 365 * $gaji;
        // }
        // return $thr;
    }

    public function excel()
    {
        $nama_file = 'THR_LEBARAN_NON_OS_2026.xlsx';
        return Excel::download(new THRLebaranExport($this->cutOffDate), $nama_file,);
    }
    public function render()
    {
        $query = Karyawan::whereNotIn('etnis', ['China', 'Tionghoa'])
            ->whereIn('status_karyawan', ['PKWT', 'PKWTT'])
            ->where('tanggal_bergabung', '<', $this->cutoffMinus30);

        $total = $query->get()->sum(function ($d) {
            return $this->hitungTHR(
                $d->id_karyawan,
                $d->tanggal_bergabung,
                $d->gaji_pokok,
                $this->cutOffDate
            );
        });

        $karyawans = (clone $query)->orderBy('tanggal_bergabung', 'desc')->paginate(10);
        return view('livewire.hitungthrlebaran', [
            'karyawans' => $karyawans,
            'total' => $total,
            'cutOffDate' => $this->cutOffDate,
        ]);
    }
}
