<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Karyawan;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\THRExport;
use Carbon\Carbon;

class Hitungthrlebaran extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $cutOffDate;
    public $cutoffMinus30;

    public function mount()
    {
        $this->cutOffDate = '2026-03-21';
        $this->cutoffMinus30 = Carbon::parse($this->cutOffDate)->subDays(30);
    }

    public function excel()
    {
        $nama_file = 'THR_NON_OS_2026.xlsx';
        return Excel::download(new THRExport($this->cutOffDate), $nama_file,);
    }
    public function render()
    {
        $query = Karyawan::whereNotIn('etnis', ['China', 'Tionghoa'])
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
        return view('livewire.hitungthrlebaran', [
            'karyawans' => $karyawans,
            'total' => $total,
            'cutOffDate' => $this->cutOffDate,
        ]);
    }
}
