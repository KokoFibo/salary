<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Rekapbackup;
use App\Models\Yfrekappresensi;
use Illuminate\Support\Facades\DB;

class MovePresensiData extends Component
{
    public $month, $year, $today;

    public $tahun, $bulan, $getYear, $getMonth, $dataBulan, $dataTahun, $totalData;

    public function cancel()
    {
        $this->today = now();
        $this->year = now()->year;
        $this->month = now()->month;
        $this->getYear = "";
        $this->getMonth = "";
        $this->dataTahun = Yfrekappresensi::selectRaw('YEAR(date) as year')
            ->groupByRaw('YEAR(date)')
            ->pluck('year')
            ->all();

        // $this->render();
    }
    public function move()
    {
        $datas = Yfrekappresensi::whereYear('date', $this->getYear)->whereMonth('date', $this->getMonth)->get();

        // foreach ($datas as $data) {
        //     $Yfpresensidata[] = [
        //         'id' => $data->id,
        //         'karyawan_id' => $data->karyawan_id,
        //         'user_id' => $data->user_id,
        //         'date' => $data->date,
        //         'first_in' => $data->first_in,
        //         'first_out' => $data->first_out,
        //         'second_in' => $data->second_in,
        //         'second_out' => $data->second_out,
        //         'overtime_in' => $data->overtime_in,
        //         'overtime_out' => $data->overtime_out,
        //         'late' => $data->late,
        //         'no_scan' => $data->no_scan,
        //         'shift' => $data->shift,
        //         'no_scan_history' => $data->no_scan_history,
        //         'late_history' => $data->late_history,
        //     ];
        // }

        foreach ($datas as $data) {
            DB::table('rekapbackups')->insert([
                'id' => $data->id,
                'karyawan_id' => $data->karyawan_id,
                'user_id' => $data->user_id,
                'date' => $data->date,
                'first_in' => $data->first_in,
                'first_out' => $data->first_out,
                'second_in' => $data->second_in,
                'second_out' => $data->second_out,
                'overtime_in' => $data->overtime_in,
                'overtime_out' => $data->overtime_out,
                'late' => $data->late,
                'no_scan' => $data->no_scan,
                'shift' => $data->shift,
                'no_scan_history' => $data->no_scan_history,
                'late_history' => $data->late_history,
            ]);
        }



        // foreach (array_chunk($Yfpresensidata, 25) as $item) {
        //     Rekapbackup::insert($item);
        // }

        // try {
        //     foreach (array_chunk($Yfpresensidata, 200) as $item) {
        //         Rekapbackup::insert($item);
        //     }
        // } catch (\Exception $e) {
        //     dd('eror lho');
        //     return back()->with('error', $e);
        // }




        foreach ($datas as $data) {
            Yfrekappresensi::where('id', $data->id)->delete();
        }

        // $this->dispatch('success', message: $this->totalData . ' Data rekap presensi sudah di move');
        $this->dispatch(
            'message',
            type: 'success',
            title: 'Data rekap presensi sudah di move',
        );
    }

    public function mount()
    {
        $this->today = now();
        $this->year = now()->year;
        $this->month = now()->month;
        $this->getYear = "";
        $this->getMonth = "";
        $this->totalData = 0;
        $this->dataTahun = Yfrekappresensi::selectRaw('YEAR(date) as year')
            ->groupByRaw('YEAR(date)')
            ->pluck('year')
            ->all();
    }

    public function updatedGetMonth()
    {
        $this->totalData = Yfrekappresensi::whereYear('date', $this->getYear)->whereMonth('date', $this->getMonth)->count();
    }
    public function updatedGetYear()
    {

        $currentMonth = $this->month;
        $lastMonth = ($currentMonth - 1) == 0 ? 12 : ($currentMonth - 1);

        $this->dataBulan = Yfrekappresensi::whereYear('date', $this->getYear)
            ->whereNotIn(DB::raw('MONTH(date)'), [$currentMonth, $lastMonth])
            ->selectRaw('MONTH(date) as month')
            ->groupByRaw('MONTH(date)')
            ->pluck('month')
            ->all();
    }

    public function render()
    {
        return view('livewire.move-presensi-data');
    }
}
