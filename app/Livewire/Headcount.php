<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Payroll;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Headcount extends Component
{
    public $select_month, $select_year;
    public $month, $year, $headcount;
    public $yig_hr, $yig_finance, $yig_procurement, $yig_ga, $yig_legal, $yig_exim, $yig_me, $yig_bd, $yig_qc, $yig_production, $yig_warehouse, $yig_bod, $yig_total;
    public $ycme_hr, $ycme_finance, $ycme_procurement, $ycme_ga, $ycme_legal, $ycme_exim, $ycme_me, $ycme_bd, $ycme_qc, $ycme_production, $ycme_warehouse, $ycme_bod, $ycme_total;
    public $ysm_hr, $ysm_finance, $ysm_procurement, $ysm_ga, $ysm_legal, $ysm_exim, $ysm_me, $ysm_bd, $ysm_qc, $ysm_production, $ysm_warehouse, $ysm_bod, $ysm_total;
    public $yam_hr, $yam_finance, $yam_procurement, $yam_ga, $yam_legal, $yam_exim, $yam_me, $yam_bd, $yam_qc, $yam_production, $yam_warehouse, $yam_bod, $yam_total;
    public $yig_total1, $ycme_total1, $ysm_total1, $yam_total1;
    public $dept, $bulan, $tahun;


    public function mount()
    {
        $payroll = Payroll::orderBy('date', 'desc')->first();
        $this->year = Carbon::parse($payroll->date)->year;
        $this->month = Carbon::parse($payroll->date)->month;
        $this->select_year = Payroll::select(DB::raw('YEAR(date) as year'))
            ->distinct()
            ->pluck('year')
            ->toArray();

        $this->select_month = Payroll::select(DB::raw('MONTH(date) as month'))
            ->whereYear('date', $this->year)
            ->distinct()
            ->orderBy('date', 'desc')
            ->pluck('month')
            ->toArray();
    }

    public function ycme()
    {
        $this->ycme_hr = 0;
        $this->ycme_finance = 0;
        $this->ycme_procurement = 0;
        $this->ycme_ga = 0;
        $this->ycme_legal = 0;
        $this->ycme_exim = 0;
        $this->ycme_me = 0;
        $this->ycme_bd = 0;
        $this->ycme_qc = 0;
        $this->ycme_production = 0;
        $this->ycme_warehouse = 0;
        $this->ycme_bod = 0;
        $this->ycme_total = 0;

        $this->ycme_hr = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YCME')->where('departemen', 'HR')->count();
        $this->ycme_finance = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YCME')->where('departemen', 'Finance Accounting')->count();
        $this->ycme_procurement = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YCME')->where('departemen', 'Procurement')->count();
        $this->ycme_ga = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YCME')->where('departemen', 'GA')->count();
        $this->ycme_legal = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YCME')->where('departemen', 'Legal')->count();
        $this->ycme_exim = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YCME')->where('departemen', 'EXIM')->count();
        $this->ycme_me = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YCME')->where('departemen', 'Engineering')->count();
        $this->ycme_bd = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YCME')->where('departemen', 'BD')->count();
        $this->ycme_qc = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YCME')->where('departemen', 'Quality Control')->count();
        $this->ycme_production = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YCME')->where('departemen', 'Produksi')->count();
        $this->ycme_warehouse = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YCME')->where('departemen', 'Gudang')->count();
        $this->ycme_bod = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YCME')->where('departemen', 'Board of Director')->count();
        $this->ycme_total1 = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YCME')->count();
        $this->ycme_total = $this->ycme_hr + $this->ycme_finance + $this->ycme_procurement + $this->ycme_ga + $this->ycme_legal + $this->ycme_exim + $this->ycme_me + $this->ycme_bd + $this->ycme_qc + $this->ycme_production + $this->ycme_warehouse + $this->ycme_bod + $this->ycme_total;
    }
    public function yig()
    {
        $this->yig_hr = 0;
        $this->yig_finance = 0;
        $this->yig_procurement = 0;
        $this->yig_ga = 0;
        $this->yig_legal = 0;
        $this->yig_exim = 0;
        $this->yig_me = 0;
        $this->yig_bd = 0;
        $this->yig_qc = 0;
        $this->yig_production = 0;
        $this->yig_warehouse = 0;
        $this->yig_bod = 0;
        $this->yig_total = 0;

        $this->yig_hr = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YIG')->where('departemen', 'HR')->count();
        $this->yig_finance = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YIG')->where('departemen', 'Finance Accounting')->count();
        $this->yig_procurement = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YIG')->where('departemen', 'Procurement')->count();
        $this->yig_ga = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YIG')->where('departemen', 'GA')->count();
        $this->yig_legal = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YIG')->where('departemen', 'Legal')->count();
        $this->yig_exim = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YIG')->where('departemen', 'EXIM')->count();
        $this->yig_me = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YIG')->where('departemen', 'Engineering')->count();
        $this->yig_bd = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YIG')->where('departemen', 'BD')->count();
        $this->yig_qc = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YIG')->where('departemen', 'Quality Control')->count();
        $this->yig_production = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YIG')->where('departemen', 'Produksi')->count();
        $this->yig_warehouse = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YIG')->where('departemen', 'Gudang')->count();
        $this->yig_bod = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YIG')->where('departemen', 'Board of Director')->count();
        $this->yig_total1 = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YIG')->count();
        $this->yig_total = $this->yig_hr + $this->yig_finance + $this->yig_procurement + $this->yig_ga + $this->yig_legal + $this->yig_exim + $this->yig_me + $this->yig_bd + $this->yig_qc + $this->yig_production + $this->yig_warehouse + $this->yig_bod + $this->yig_total;
    }
    public function ysm()
    {
        $this->ysm_hr = 0;
        $this->ysm_finance = 0;
        $this->ysm_procurement = 0;
        $this->ysm_ga = 0;
        $this->ysm_legal = 0;
        $this->ysm_exim = 0;
        $this->ysm_me = 0;
        $this->ysm_bd = 0;
        $this->ysm_qc = 0;
        $this->ysm_production = 0;
        $this->ysm_warehouse = 0;
        $this->ysm_bod = 0;
        $this->ysm_total = 0;

        $this->ysm_hr = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YSM')->where('departemen', 'HR')->count();
        $this->ysm_finance = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YSM')->where('departemen', 'Finance Accounting')->count();
        $this->ysm_procurement = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YSM')->where('departemen', 'Procurement')->count();
        $this->ysm_ga = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YSM')->where('departemen', 'GA')->count();
        $this->ysm_legal = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YSM')->where('departemen', 'Legal')->count();
        $this->ysm_exim = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YSM')->where('departemen', 'EXIM')->count();
        $this->ysm_me = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YSM')->where('departemen', 'Engineering')->count();
        $this->ysm_bd = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YSM')->where('departemen', 'BD')->count();
        $this->ysm_qc = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YSM')->where('departemen', 'Quality Control')->count();
        $this->ysm_production = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YSM')->where('departemen', 'Produksi')->count();
        $this->ysm_warehouse = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YSM')->where('departemen', 'Gudang')->count();
        $this->ysm_bod = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YSM')->where('departemen', 'Board of Director')->count();
        $this->ysm_total1 = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YSM')->count();
        $this->ysm_total = $this->ysm_hr + $this->ysm_finance + $this->ysm_procurement + $this->ysm_ga + $this->ysm_legal + $this->ysm_exim + $this->ysm_me + $this->ysm_bd + $this->ysm_qc + $this->ysm_production + $this->ysm_warehouse + $this->ysm_bod + $this->ysm_total;
    }
    public function yam()
    {
        $this->yam_hr = 0;
        $this->yam_finance = 0;
        $this->yam_procurement = 0;
        $this->yam_ga = 0;
        $this->yam_legal = 0;
        $this->yam_exim = 0;
        $this->yam_me = 0;
        $this->yam_bd = 0;
        $this->yam_qc = 0;
        $this->yam_production = 0;
        $this->yam_warehouse = 0;
        $this->yam_bod = 0;
        $this->yam_total = 0;

        $this->yam_hr = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YAM')->where('departemen', 'HR')->count();
        $this->yam_finance = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YAM')->where('departemen', 'Finance Accounting')->count();
        $this->yam_procurement = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YAM')->where('departemen', 'Procurement')->count();
        $this->yam_ga = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YAM')->where('departemen', 'GA')->count();
        $this->yam_legal = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YAM')->where('departemen', 'Legal')->count();
        $this->yam_exim = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YAM')->where('departemen', 'EXIM')->count();
        $this->yam_me = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YAM')->where('departemen', 'Engineering')->count();
        $this->yam_bd = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YAM')->where('departemen', 'BD')->count();
        $this->yam_qc = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YAM')->where('departemen', 'Quality Control')->count();
        $this->yam_production = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YAM')->where('departemen', 'Produksi')->count();
        $this->yam_warehouse = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YAM')->where('departemen', 'Gudang')->count();
        $this->yam_bod = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YAM')->where('departemen', 'Board of Director')->count();
        $this->yam_total1 = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->where('company', 'YAM')->count();
        $this->yam_total = $this->yam_hr + $this->yam_finance + $this->yam_procurement + $this->yam_ga + $this->yam_legal + $this->yam_exim + $this->yam_me + $this->yam_bd + $this->yam_qc + $this->yam_production + $this->yam_warehouse + $this->yam_bod + $this->yam_total;
    }

    public function excel()
    {
        // public $ysm_hr, $ysm_finance, $ysm_procurement, $ysm_ga, $ysm_legal, $ysm_exim, $ysm_me, $ysm_bd, $ysm_qc, $ysm_production, $ysm_warehouse, $ysm_bod, $ysm_total;
        // public $yam_hr, $yam_finance, $yam_procurement, $yam_ga, $yam_legal, $yam_exim, $yam_me, $yam_bd, $yam_qc, $yam_production, $yam_warehouse, $yam_bod, $yam_total;
        $this->yig();
        $this->ycme();
        $this->ysm();
        $this->yam();
        $this->headcount = 0;
        $this->headcount = Payroll::whereYear('date', $this->year)->whereMonth('date', $this->month)->count();
        $this->bulan = $this->month;
        $this->tahun = $this->year;
    }

    // public function updatedSelectYear()
    // {
    //     $this->year = $this->select_year;
    //     $this->select_year = Payroll::select(DB::raw('YEAR(date) as year'))
    //         ->distinct()
    //         ->pluck('year')
    //         ->toArray();
    //     $this->select_month = Payroll::select(DB::raw('MONTH(date) as month'))
    //         ->whereYear('date', $this->year)
    //         ->distinct()
    //         ->orderBy('date', 'desc')
    //         ->pluck('month')
    //         ->toArray();
    // }

    public function render()
    {

        $this->select_year = Payroll::select(DB::raw('YEAR(date) as year'))
            ->distinct()
            ->pluck('year')
            ->toArray();

        $this->select_month = Payroll::select(DB::raw('MONTH(date) as month'))
            ->whereYear('date', $this->year)
            ->distinct()
            ->orderBy('date', 'desc')
            ->pluck('month')
            ->toArray();

        $this->dept =
            Payroll::distinct()
            ->pluck('departemen')
            ->toArray();


        return view('livewire.headcount');
    }
}
