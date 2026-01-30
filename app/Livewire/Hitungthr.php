<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Karyawan;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\THRExport;
use Carbon\Carbon;



class Hitungthr extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $cutOffDate;
    public $cutoffMinus30;

    public function mount()
    {
        $this->cutOffDate = '2026-02-17';
        $this->cutoffMinus30 = Carbon::parse($this->cutOffDate)->subDays(30);
    }

    public function excel()
    {
        $nama_file = 'THR_NON_OS_2026.xlsx';
        return Excel::download(new THRExport($this->cutOffDate), $nama_file,);
    }

    public function render()
    {
        // $id_kecuali = [2, 4, 6435];
        $query = Karyawan::whereIn('etnis', ['China', 'Tionghoa'])
            ->whereIn('status_karyawan', ['PKWT', 'PKWTT'])
            ->where('tanggal_bergabung', '<', $this->cutoffMinus30);

        $total = $query->get()->sum(function ($d) {
            return hitungTHR(
                $d->id_karyawan,
                $d->tanggal_bergabung,
                $d->gaji_pokok,
                $this->cutOffDate
            );
        });

        $karyawans = (clone $query)->orderBy('tanggal_bergabung', 'desc')->paginate(10);

        return view('livewire.hitungthr', [
            'karyawans' => $karyawans,
            'total' => $total,
            'cutOffDate' => $this->cutOffDate,
        ]);
    }
}
