<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Lock;
use App\Models\Company;
use App\Models\Payroll;
use Livewire\Component;
use App\Jobs\rebuildJob;
use App\Models\Karyawan;
use App\Models\Tambahan;
use App\Models\Placement;
use App\Exports\PphExport;
use App\Models\Department;
use App\Models\Jamkerjaid;
use Livewire\WithPagination;
use App\Jobs\BuildPayrollJob;
use App\Exports\PayrollExport;
use App\Models\Yfrekappresensi;
use App\Exports\BankReportExcel;
use App\Exports\PlacementExport;
use App\Exports\DepartmentExport;
use App\Exports\ExcelDetailReport;
use Illuminate\Support\Facades\DB;
use App\Exports\ExcelDetailReport2;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PlacementExportUtama;



class Payrollwr extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $selected_company = 0;
    public $selected_placement = 0;
    public $selected_departemen = 0;
    public $selected_etnis = '0';
    // public $departments;
    public $search;
    public $perpage = 10;
    public $month;
    public $year;
    public $columnName = 'id_karyawan';
    public $direction = 'asc';
    public $status = 1;
    public $data_payroll;
    public $data_karyawan;
    public $cx;
    public $lock_presensi;
    public $lock_slip_gaji;
    public $lock_data;
    public $select_month, $select_year;

    public function buat_payroll_baru()
    {
        build_payroll_os_new($this->month, $this->year);
    }

    public function bulan()
    {
        $this->year ??= now()->year;

        $payrollTerakhir = Payroll::latest('date')->first();

        $this->select_month = Payroll::select(DB::raw('MONTH(date) as month'))
            ->whereYear('date', $this->year)
            ->distinct()
            ->pluck('month')
            ->filter() // Filter null jika ada
            ->toArray();

        // Jika null tetap aman
        $this->select_month = $this->select_month ?? [];

        if ($payrollTerakhir &&  $this->year == now()->year) {
            $tanggalTerakhir = Carbon::parse($payrollTerakhir->date);
            $tanggalSekarang = Carbon::now();

            $selisihBulan = $tanggalSekarang->diffInMonths($tanggalTerakhir);
            if ($selisihBulan == 2) {
                $bulanSetelah = $tanggalTerakhir->copy()->addMonth();
                $bulanTambahan = (int) $bulanSetelah->format('m');

                if (!in_array($bulanTambahan, $this->select_month)) {
                    $this->select_month[] = $bulanTambahan;
                }
            }
        }
    }

    public function excelDetailReport()
    {
        $nama_file = 'Non OS Detail Report ' . nama_bulan($this->month) . ' ' . $this->year . '.xlsx';
        return Excel::download(new ExcelDetailReport($this->month, $this->year), $nama_file);
    }
    public function excelDetailReport2()
    {
        $nama_file = 'Non OS Detail Report ' . nama_bulan($this->month) . ' ' . $this->year . '.xlsx';
        return Excel::download(new ExcelDetailReport2($this->month, $this->year), $nama_file);
    }

    public function clear_lock()
    {
        clear_locks();
        delete_failed_jobs();
    }

    public function close_succesful_rebuilt()
    {
        $rebuild = Lock::find(1);
        $rebuild->rebuild_done = false;
        $rebuild->save();
    }


    public function export()
    {
        $nama_file = '';
        if ($this->selected_company != 0) {
            if ($this->selected_company == 0) {
                $nama_file = 'semua_payroll.xlsx';
            } else {
                $nama_file = 'payroll_company_' . nama_company($this->selected_company) . '.xlsx';
            }
        } elseif ($this->selected_placement != 0) {
            if ($this->selected_placement == 0) {
                $nama_file = 'semua_payroll.xlsx';
            } else {
                $nama_file = 'payroll_Directorate_' . nama_placement($this->selected_placement) . '.xlsx';
            }
        } else {
            if ($this->selected_departemen == 0) {
                $nama_file = 'semua_payroll.xlsx';
            } else {
                $nama_file = 'payroll_department_' . sambungKata($this->selected_departemen) . '.xlsx';
            }
        }

        $nama_file = nama_file_excel($nama_file, $this->month, $this->year);


        // if ($this->selected_company != 0) {
        //     return Excel::download(new PayrollExport($this->selected_company, $this->status, $this->month, $this->year), $nama_file);
        // } else if ($this->selected_placement != 0) {
        //     return Excel::download(new PlacementExport($this->selected_placement, $this->status, $this->month, $this->year), $nama_file);
        // } else {
        //     return Excel::download(new DepartmentExport($this->selected_departemen, $this->status, $this->month, $this->year), $nama_file);
        // }
        return Excel::download(new PlacementExportUtama($this->selected_company, $this->selected_placement, $this->selected_departemen, $this->status, $this->month, $this->year), $nama_file);
    }




    public function bankexcel()
    {
        $nama_file = '';
        if ($this->selected_company != 0) {
            if ($this->selected_company == 0) {
                $payroll = Payroll::whereIn('status_karyawan', ['PKWT', 'PKWTT', 'Dirumahkan', 'Resigned'])
                    ->whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->orderBy('id_karyawan', 'asc')
                    ->get(['nama', 'nama_bank', 'nomor_rekening', 'total', 'company_id', 'placement_id', 'department_id']);
                $nama_file = 'semua_karyawan_Bank.xlsx';
            } else {
                $payroll = Payroll::whereIn('status_karyawan', ['PKWT', 'PKWTT', 'Dirumahkan', 'Resigned'])
                    ->whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->orderBy('id_karyawan', 'asc')
                    ->where('company_id', $this->selected_company)
                    ->get(['nama', 'nama_bank', 'nomor_rekening', 'total', 'company_id', 'placement_id', 'department_id']);

                $nama_file = nama_company($this->selected_company) . '_Company_Bank.xlsx';
            }
        } elseif ($this->selected_placement != 0) {
            if ($this->selected_placement == 0) {
                $payroll = Payroll::whereIn('status_karyawan', ['PKWT', 'PKWTT', 'Dirumahkan', 'Resigned'])
                    ->whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->orderBy('id_karyawan', 'asc')
                    ->get(['nama', 'nama_bank', 'nomor_rekening', 'total', 'company_id', 'placement_id', 'department_id']);

                $nama_file = 'semua_karyawan_Bank.xlsx';
            } else {
                $payroll = Payroll::whereIn('status_karyawan', ['PKWT', 'PKWTT', 'Dirumahkan', 'Resigned'])
                    ->whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->orderBy('id_karyawan', 'asc')
                    ->where('placement_id', $this->selected_placement)
                    ->get(['nama', 'nama_bank', 'nomor_rekening', 'total', 'company_id', 'placement_id', 'department_id']);

                $nama_file = nama_placement($this->selected_placement) . '_Directorate_Bank.xlsx';
            }
        } else {
            if ($this->selected_departemen == 0) {
                $payroll = Payroll::whereIn('status_karyawan', ['PKWT', 'PKWTT', 'Dirumahkan', 'Resigned'])
                    ->whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->orderBy('id_karyawan', 'asc')
                    ->get(['nama', 'nama_bank', 'nomor_rekening', 'total', 'company_id', 'placement_id', 'department_id']);

                $nama_file = 'semua_karyawan_Bank.xlsx';
            } else {
                $payroll = Payroll::whereIn('status_karyawan', ['PKWT', 'PKWTT', 'Dirumahkan', 'Resigned'])
                    ->whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->orderBy('id_karyawan', 'asc')
                    ->where('department_id', $this->selected_departemen)
                    ->get(['nama', 'nama_bank', 'nomor_rekening', 'total', 'company_id', 'placement_id', 'department_id']);


                $nama_file = sambungKata($this->selected_departemen) . '_Department_Bank.xlsx';
            }
        }

        $nama_file = nama_file_excel($nama_file, $this->month, $this->year);


        return Excel::download(new BankReportExcel($this->status, $this->month, $this->year, $this->selected_company, $this->selected_placement, $this->selected_departemen, $this->selected_etnis), $nama_file,);
    }

    public function showDetail($id_karyawan)
    {

        $this->data_payroll = Payroll::with('jamkerjaid')
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->where('id_karyawan', $id_karyawan)
            ->first();

        $this->data_karyawan = Karyawan::where('id_karyawan', $id_karyawan)->first();
        // dd($this->data_karyawan);
    }

    public function sortColumnName($namaKolom)
    {
        $this->columnName = $namaKolom;
        $this->direction = $this->swapDirection();
    }
    public function swapDirection()
    {
        return $this->direction === 'asc' ? 'desc' : 'asc';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        // $this->departments = Karyawan::select('department_id')
        //     ->distinct()
        //     ->pluck('department_id')
        //     ->toArray();
        $this->bulan();
        $data = Payroll::first();
        if (now()->day < 5) {
            $this->year =
                now()->subMonth()->year;
            $this->month =
                now()->subMonth()->month;
        } else {
            $data3 = Payroll::orderBy('date', 'desc')->first();
            // $this->year = now()->year;
            // $this->month = now()->month;
            $this->year = Carbon::parse($data3->date)->year;
            $this->month = Carbon::parse($data3->date)->month;
        }

        if ($data != null) {
            $this->data_payroll = Payroll::with('jamkerjaid')
                ->whereMonth('date', $this->month)
                ->whereYear('date', $this->year)
                ->where('id_karyawan', $data->id_karyawan)
                ->first();
            $this->data_karyawan = Karyawan::where('id_karyawan', $data->id_karyawan)->first();
        }

        $lock = Lock::find(1);
        $this->lock_presensi = $lock->presensi;
        $this->lock_slip_gaji = $lock->slip_gaji;
        $this->lock_data = $lock->data;
    }
    public function updatedLockPresensi()
    {
        // $lock=Lock::find(1);
        $lock = Lock::first();
        $lock->presensi = $this->lock_presensi;
        $lock->save();
    }
    public function updatedLockSlipGaji()
    {
        // $lock=Lock::find(1);
        $lock = Lock::first();
        $lock->slip_gaji = $this->lock_slip_gaji;
        $lock->save();
    }
    public function updatedLockData()
    {
        // $lock=Lock::find(1);
        $lock = Lock::first();
        $lock->data = $this->lock_data;
        $lock->save();
    }

    public function buat_payroll($queue)
    {

        // supaya tidak dilakukan bersamaan
        if (check_absensi_kosong()) {
            clear_locks();
            // $this->dispatch('error', message: 'Masih ada data kosong di presensi');
            $this->dispatch(
                'message',
                type: 'error',
                title: 'Masih ada data kosong di presensi',
            );
            return;
        }
        $lock = Lock::find(1);
        if ($lock->build == 1) {
            // $this->dispatch('error', message: 'Mohon dicoba sebentar lagi');
            $this->dispatch(
                'message',
                type: 'error',
                title: 'Mohon dicoba sebentar lagi',
            );
            return;
        } else {
            $lock->build = 1;
            $lock->save();
        }

        // hapus ini kalau sudah kelar develop
        $lock->build = 1;
        $lock->save();

        $startTime = microtime(true);
        if ($queue == 'noQueue') {
            $result  = build_payroll($this->month, $this->year);
        } else {
            $lock = Lock::find(1);
            $lock->rebuild_done = 2;
            $lock->save();
            dispatch(new rebuildJob($this->month, $this->year));
            $result = 1;
        }


        if ($result == 0) {
            // $this->dispatch('error', message: 'Data Presensi tidak ada');
            $this->dispatch(
                'message',
                type: 'error',
                title: 'Data Presensi tidak ada',
            );
        } else {
            // $this->dispatch('success', message: 'Data Payroll Karyawan Sudah di Built ( ' . number_format((microtime(true) - $startTime), 2) . ' seconds )');
            if ($queue == 'noQueue') {
                $this->dispatch(
                    'message',
                    type: 'success',
                    title: 'Data Payroll Karyawan Sudah di Built ( ' . number_format((microtime(true) - $startTime), 2) . ' seconds )',
                );
            }
        }


        $lock->build = 0;
        $lock->save();
        $this->mount();
    }



    // ok3
    public function bonus_potongan()
    {
        $bonus = 0;
        $potongaan = 0;
        $all_bonus = 0;
        $all_potongan = 0;
        $tambahan = Tambahan::whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->get();

        foreach ($tambahan as $d) {
            $all_bonus = $d->uang_makan + $d->bonus_lain;
            $all_potongan = $d->baju_esd + $d->gelas + $d->sandal + $d->seragam + $d->sport_bra + $d->hijab_instan + $d->id_card_hilang + $d->masker_hijau + $d->potongan_lain;
            $id_payroll = Payroll::whereMonth('date', $this->month)
                ->whereYear('date', $this->year)
                ->where('id_karyawan', $d->user_id)
                ->first();
            if ($id_payroll != null) {
                $payroll = Payroll::find($id_payroll->id);
                $payroll->bonus1x = $payroll->bonus1x + $all_bonus;
                $payroll->potongan1x = $payroll->potongan1x + $all_potongan;
                $payroll->total = $payroll->total + $all_bonus - $all_potongan;
                $payroll->save();
            }
        }

        // $this->dispatch('success', message: 'Bonus dan Potangan added');
        $this->dispatch(
            'message',
            type: 'success',
            title: 'Bonus dan Potangan added',
        );
    }

    // public function getPayrollQuery($statuses, $search = null, $placement_id = null, $company_id = null, $department_id = null)
    // {
    //     return Payroll::query()

    //         ->whereIn('status_karyawan', $statuses)
    //         ->when($search, function ($query) use ($search) {
    //             $query
    //                 // ->where('id_karyawan', 'LIKE', '%' . trim($search) . '%')
    //                 ->where('id_karyawan',  trim($search))
    //                 ->orWhere('nama', 'LIKE', '%' . trim($search) . '%')
    //                 ->orWhere('jabatan_id', 'LIKE', '%' . trim($search) . '%')
    //                 ->orWhere('company_id', 'LIKE', '%' . trim($search) . '%')
    //                 ->orWhere('department_id', 'LIKE', '%' . trim($search) . '%')
    //                 ->orWhere('metode_penggajian', 'LIKE', '%' . trim($search) . '%')
    //                 ->orWhere('status_karyawan', 'LIKE', '%' . trim($search) . '%');
    //         })
    //         ->when($placement_id, function ($query) use ($placement_id) {
    //             $query->where('placement_id', $placement_id);
    //         })
    //         ->when($company_id, function ($query) use ($company_id) {
    //             $query->where('company_id', $company_id);
    //         })
    //         ->when($department_id, function ($query) use ($department_id) {
    //             $query->where('department_id', $department_id);
    //         })

    //         ->orderBy($this->columnName, $this->direction);
    // }

    public function getPayrollQuery($statuses, $search = null, $placement_id = null, $company_id = null, $department_id = null)
    {
        return Payroll::query()
            ->whereIn('status_karyawan', $statuses)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $trimmedSearch = trim($search);
                    $subQuery->where('id_karyawan', $trimmedSearch)
                        ->orWhere('nama', 'LIKE', "%{$trimmedSearch}%")
                        ->orWhere('jabatan_id', 'LIKE', "%{$trimmedSearch}%")
                        ->orWhere('company_id', 'LIKE', "%{$trimmedSearch}%")
                        ->orWhere('department_id', 'LIKE', "%{$trimmedSearch}%")
                        ->orWhere('metode_penggajian', 'LIKE', "%{$trimmedSearch}%")
                        ->orWhere('status_karyawan', 'LIKE', "%{$trimmedSearch}%");
                });
            })
            ->when($placement_id, fn($query) => $query->where('placement_id', $placement_id))
            ->when($company_id, fn($query) => $query->where('company_id', $company_id))
            ->when($department_id, fn($query) => $query->where('department_id', $department_id))
            ->orderBy($this->columnName, $this->direction);
    }


    public function updatedSelectedCompany()
    {
        $this->selected_placement = 0;
        $this->selected_departemen = 0;
    }
    public function updatedSelectedPlacement()
    {
        $this->selected_company = 0;
        $this->selected_departemen = 0;
    }
    public function updatedSelectedDepartemen()
    {
        $this->selected_company = 0;
        $this->selected_placement = 0;
    }

    public function updatedYear()
    {
        // $this->select_month = Payroll::select(DB::raw('MONTH(date) as month'))->whereYear('date', $this->year)
        //     ->distinct()
        //     ->pluck('month')
        //     ->toArray();

        // $this->month = $this->select_month[0];
        $this->bulan();
    }

    public function render()
    {

        $this->cx++;

        $this->select_year = Payroll::select(DB::raw('YEAR(date) as year'))
            ->distinct()
            ->pluck('year')
            ->toArray();
        // ooo
        // $this->select_month = Payroll::select(DB::raw('MONTH(date) as month'))->whereYear('date', $this->year)
        //     ->distinct()
        //     ->pluck('month')
        //     ->toArray();
        $months = Payroll::select(DB::raw('MONTH(date) as month'))
            ->whereYear('date', $this->year)
            ->distinct()
            ->orderBy('date', 'desc')
            ->pluck('month')
            ->toArray();

        // $data_bulan_ini = Yfrekappresensi::whereYear('date', now()->year)
        //     ->whereMonth('date', now()->month)->count();

        // if ($data_bulan_ini > 0) {
        //     $months[] = $this->month;
        // }
        // if (!in_array($this->month, $months)) {
        //     $months[] = $this->month;
        // }

        // $this->select_month = $months;

        if ($this->status == 1) {
            $statuses = ['PKWT', 'PKWTT', 'Dirumahkan', 'Resigned'];
        } elseif ($this->status == 2) {
            $statuses = ['Blacklist'];
        } else {
            $statuses = ['PKWT', 'PKWTT', 'Dirumahkan', 'Resigned', 'Blacklist'];
        }

        if ($this->selected_placement == 0 && $this->selected_departemen == 0) {
            if ($this->selected_company == 0) {
                $total = Payroll::whereIn('status_karyawan', $statuses)
                    ->whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->sum('total');

                $payroll = $this->getPayrollQuery($statuses, $this->search)
                    ->whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->orderBy($this->columnName, $this->direction)
                    ->paginate($this->perpage);
            } else {
                $total = Payroll::whereIn('status_karyawan', $statuses)
                    ->whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->where('company_id', $this->selected_company)
                    ->sum('total');

                $payroll = $this->getPayrollQuery($statuses, $this->search, '', $this->selected_company)
                    ->whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->orderBy($this->columnName, $this->direction)
                    ->paginate($this->perpage);
            }
        } elseif ($this->selected_company == 0 && $this->selected_departemen == 0) {
            if ($this->selected_placement == 0) {
                $total = Payroll::whereIn('status_karyawan', $statuses)
                    ->whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->sum('total');

                $payroll = $this->getPayrollQuery($statuses, $this->search)
                    ->whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->orderBy($this->columnName, $this->direction)
                    ->paginate($this->perpage);
            } else {
                $total = Payroll::whereIn('status_karyawan', $statuses)
                    ->whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->where('placement_id', $this->selected_placement)
                    ->sum('total');

                $payroll = $this->getPayrollQuery($statuses, $this->search, $this->selected_placement, '', '')
                    ->whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->orderBy($this->columnName, $this->direction)
                    ->paginate($this->perpage);
            }
        } else {
            if ($this->selected_departemen == 0) {
                $total = Payroll::whereIn('status_karyawan', $statuses)
                    ->whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->sum('total');

                $payroll = $this->getPayrollQuery($statuses, $this->search)
                    ->whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->orderBy($this->columnName, $this->direction)
                    ->paginate($this->perpage);
            } else {


                $total = Payroll::whereIn('status_karyawan', $statuses)
                    ->where('department_id', $this->selected_departemen)
                    ->whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->sum('total');
                $payroll = $this->getPayrollQuery($statuses, $this->search, '', '', $this->selected_departemen)

                    ->where('department_id', $this->selected_departemen)
                    ->whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->orderBy($this->columnName, $this->direction)
                    ->paginate($this->perpage);
            }
        }



        $tgl = Payroll::whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->select('created_at')->first();
        if ($tgl != null) {
            $last_build = Carbon::parse($tgl->created_at)->diffForHumans();
        } else {
            $last_build = 0;
        }

        $data_kosong = Jamkerjaid::count();


        $this->cx++;

        $companies = Company::orderBy('company_name', 'ASC')->get();
        $placements = Placement::orderBy('placement_name', 'ASC')->get();
        $departments = Department::orderBy('nama_department', 'ASC')->get();
        return view('livewire.payrollwr', compact([
            'payroll',
            'total',
            'last_build',
            'data_kosong',
            // 'data_bulan_ini',
            'companies',
            'departments',
            'placements',
        ]));
    }
}
