<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Payroll;
use Livewire\Component;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PerubahanGajiExport;


class Kenaikangaji extends Component
{
    public $etnis, $year;

    public function excel()
    {
        $nama_file = 'Perubahan Gaji Etnis ' . $this->etnis . ' Tahun ' . $this->year . '.xlsx';
        return Excel::download(new PerubahanGajiExport($this->etnis, $this->year), $nama_file);
    }

    public function mount()
    {
        $this->etnis = 'Tionghoa';
        $this->year = Carbon::now()->year;
    }

    public function render()
    {

        $karyawans = Karyawan::join('payrolls', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->select(
                'karyawans.id_karyawan',
                'karyawans.nama',
                'payrolls.date',
                'payrolls.gaji_pokok'
            )
            ->whereYear('payrolls.date', $this->year)
            ->when($this->etnis == 'Lainnya', function ($query) {
                $query->whereNotIn('karyawans.etnis', ['Tionghoa', 'China']);
            }, function ($query) {
                $query->where('karyawans.etnis', $this->etnis);
            })
            ->orderBy('karyawans.id_karyawan', 'asc')
            ->get()
            ->groupBy('id_karyawan')
            ->map(function ($dataPerKaryawan) {
                $monthlySalaries = array_fill_keys([
                    'January',
                    'February',
                    'March',
                    'April',
                    'May',
                    'June',
                    'July',
                    'August',
                    'September',
                    'October',
                    'November',
                    'December'
                ], null);

                foreach ($dataPerKaryawan as $payroll) {
                    $month = \Carbon\Carbon::parse($payroll->date)->format('F');
                    $monthlySalaries[$month] = $payroll->gaji_pokok;
                }

                return [
                    'id_karyawan' => $dataPerKaryawan->first()->id_karyawan,
                    'nama' => $dataPerKaryawan->first()->nama,
                    'gaji_per_bulan' => $monthlySalaries,
                ];
            })->values();


        return view('livewire.kenaikangaji', ['karyawans' => $karyawans]);
    }
}
