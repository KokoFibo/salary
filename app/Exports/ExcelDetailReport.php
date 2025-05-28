<?php

namespace App\Exports;

use DateTime;

use App\Models\Payroll;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ExcelDetailReport implements FromView,  ShouldAutoSize, WithColumnFormatting, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */

    use Exportable;

    protected $data;

    protected  $month, $year;
    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function fetchData($month, $year, $placement_id)
    {
        $url = "http://payroll.yifang.co.id/api/os-placement/{$month}/{$year}/{$placement_id}";

        try {
            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                $json = $response->json();

                // Cegah jika tidak ada key 'data'
                return [
                    'data' => $json['data'] ?? 0,
                    'status' => $json['status'] ?? false,
                    'message' => $json['message'] ?? null,
                ];
            } else {
                return [
                    'data' => 0,
                    'status' => false,
                    'message' => 'Gagal mengambil data dari API (status code tidak 200).',
                ];
            }
        } catch (\Exception $e) {
            return [
                'data' => 0,
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ];
        }
    }

    public function view(): View
    {
        $companies = [
            'YAM' => 7,
            'YSM' => 6,
            'YIG' => 5,
            'YCME' => 3,
            'YNE' => 101,
            'STI' => 102,
        ];
        $placements = [
            'YAM' => 5,
            'YSM' => 13,
            'YIG' => 12,
            'YCME' => 6,
            'YNE' => 106,
            'STI' => 104,
            'Pabrik 1' => 102,
            'Pabrik 2' => 10,
            'Pabrik 3' => 9,
            'Pabrik 4' => 101,
            'Pabrik 5' => 103,
            'YEV SUNRA' => 11,
            'TDB' => 105,
            'XIN XUN' => 107,
            'EXIM' => 108,
        ];
        $departments = [
            'EXIM' => 3,

        ];


        // $id_wanto = 6435;

        $date = DateTime::createFromFormat('Y-n', "$this->year-$this->month");
        $date->modify('-1 month');

        $last_month = (int) $date->format('n');
        $last_year  = (int) $date->format('Y');

        $results = [];

        foreach ($companies as $key => $company_id) {
            $results[$key] = [
                'total' => Payroll::whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->where('company_id', $company_id)
                    ->sum('total'),
                'prf' => Payroll::whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->where('company_id', $company_id)
                    ->sum('prf'),
                'core_cash' => Payroll::whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->where('company_id', $company_id)
                    ->sum('core_cash'),

                'count' => Payroll::whereMonth('date', $this->month)
                    ->whereYear('date', $this->year)
                    ->where('company_id', $company_id)
                    ->count(),
            ];
            $last_results[$key] = [
                'total' => Payroll::whereMonth('date', $last_month)
                    ->whereYear('date', $last_year)
                    ->where('company_id', $company_id)
                    ->sum('total'),


                'count' => Payroll::whereMonth('date', $last_month)
                    ->whereYear('date', $last_year)
                    ->where('company_id', $company_id)
                    ->count(),
            ];
            $selisih_results[$key] = [
                'selisih' =>   $last_results[$key]['total'] - $results[$key]['total'],
            ];
        }
        $total = 0;
        $last_total = 0;
        $count = 0;
        $last_count = 0;
        $total_selisih = 0;
        foreach ($last_results as $result) {
            $last_total += $result['total'];
            $last_count += $result['count'];
        }
        foreach ($results as $result) {
            $total += $result['total'];
            $count += $result['count'];
        }
        foreach ($selisih_results as $result) {
            $total_selisih += $result['selisih'];
        }
        $YAM = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YAM'])
            ->where('payrolls.placement_id', $placements['YAM'])
            ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');


        $YSM_CF = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YSM'])
            ->where('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        // $YSM_Wanto = DB::table('payrolls')
        //     ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
        //     ->whereMonth('payrolls.date', $this->month)
        //     ->whereYear('payrolls.date', $this->year)
        //     ->where('payrolls.company_id', $companies['YIG'])
        //     ->where('payrolls.placement_id', $placements['YSM'])

        //     ->where('karyawans.id_karyawan', $id_wanto)
        //     ->sum('payrolls.total');
        $YSM = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YSM'])
            ->where('payrolls.placement_id', $placements['YSM'])
            ->whereNot('karyawans.etnis', 'China')

            ->sum('payrolls.total');


        $YIG_CF = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YIG'])
            ->where('karyawans.etnis', 'China')
            ->sum('payrolls.total');
        $YIG = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YIG'])
            ->where('payrolls.placement_id', $placements['YIG'])
            ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        // YCME
        $YCME_CF = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YCME'])
            ->where('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $YCME = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YCME'])
            ->where('payrolls.placement_id', $placements['YCME'])
            ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');
        $YCME_exim = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YCME'])
            ->where('payrolls.placement_id', $placements['EXIM'])
            ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $YCME_XIN_XUN = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YCME'])
            ->where('payrolls.placement_id', $placements['XIN XUN'])
            // ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $YCME_Pabrik1_CF = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YCME'])
            ->where('payrolls.placement_id', $placements['Pabrik 1'])
            ->where('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $YCME_Pabrik1 = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YCME'])
            ->where('payrolls.placement_id', $placements['Pabrik 1'])
            ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $YCME_Pabrik2_CF = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YCME'])
            ->where('payrolls.placement_id', $placements['Pabrik 2'])
            ->where('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $YCME_Pabrik2 = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YCME'])
            ->where('payrolls.placement_id', $placements['Pabrik 2'])
            ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $YCME_Pabrik3_CF = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YCME'])
            ->where('payrolls.placement_id', $placements['Pabrik 3'])
            ->where('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $YCME_Pabrik3 = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YCME'])
            ->where('payrolls.placement_id', $placements['Pabrik 3'])
            ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $YCME_Pabrik4_CF = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YCME'])
            ->where('payrolls.placement_id', $placements['Pabrik 4'])
            ->where('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $YCME_Pabrik4 = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YCME'])
            ->where('payrolls.placement_id', $placements['Pabrik 4'])
            ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $YCME_Sunra_CF = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YCME'])
            ->where('payrolls.placement_id', $placements['YEV SUNRA'])
            ->where('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $YCME_Sunra = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YCME'])
            ->where('payrolls.placement_id', $placements['YEV SUNRA'])
            ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $YNE = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['YNE'])
            ->where('payrolls.placement_id', $placements['YNE'])
            // ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');
        $STI = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.company_id', $companies['STI'])
            ->where('payrolls.placement_id', $placements['STI'])
            // ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $placement_YAM = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['YAM'])
            ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');


        $placement_YSM = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['YSM'])
            ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');
        $placement_YSM_CF = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['YSM'])
            ->where('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $placement_YSM = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['YSM'])
            ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');
        $placement_YSM_CF = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['YSM'])
            ->where('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $placement_YIG = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['YIG'])
            ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');
        $placement_YIG_CF = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['YIG'])
            ->where('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $placement_YCME = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['YCME'])
            ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');
        $placement_YCME_CF = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['YCME'])
            ->where('karyawans.etnis', 'China')
            ->sum('payrolls.total');
        $placement_YCME_XIN_XUN = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['XIN XUN'])
            ->sum('payrolls.total');
        $placement_YCME_EXIM = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['EXIM'])
            ->sum('payrolls.total');

        $placement_pabrik1 = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['Pabrik 1'])
            ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');
        $placement_pabrik1_CF = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['Pabrik 1'])
            ->where('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $placement_pabrik2 = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['Pabrik 2'])
            ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');
        $placement_pabrik2_CF = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['Pabrik 2'])
            ->where('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $placement_pabrik3 = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['Pabrik 3'])
            ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');
        $placement_pabrik3_CF = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['Pabrik 3'])
            ->where('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $placement_pabrik4 = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['Pabrik 4'])
            ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');
        $placement_pabrik4_CF = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['Pabrik 4'])
            ->where('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $placement_SUNRA = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['YEV SUNRA'])
            ->whereNot('karyawans.etnis', 'China')
            ->sum('payrolls.total');
        $placement_SUNRA_CF = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['YEV SUNRA'])
            ->where('karyawans.etnis', 'China')
            ->sum('payrolls.total');

        $placement_Pabrik5_YNE = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['YNE'])
            ->sum('payrolls.total');

        $placement_STI = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['STI'])
            ->sum('payrolls.total');

        $placement_TDB = DB::table('payrolls')
            ->join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
            ->whereMonth('payrolls.date', $this->month)
            ->whereYear('payrolls.date', $this->year)
            ->where('payrolls.placement_id', $placements['TDB'])
            ->sum('payrolls.total');
        //  khusus API
        $YAM_WP = $this->fetchData(4, 2025, $placements['YAM'])['data'];
        $YSM_WP = $this->fetchData(4, 2025, $placements['YSM'])['data'];
        $YIG_WP = $this->fetchData(4, 2025, $placements['YIG'])['data'];
        $Pabrik1_WP = $this->fetchData(4, 2025, $placements['Pabrik 1'])['data'];
        $Pabrik2_WP = $this->fetchData(4, 2025, $placements['Pabrik 2'])['data'];
        $Pabrik3_WP = $this->fetchData(4, 2025, $placements['Pabrik 3'])['data'];
        $Pabrik4_WP = $this->fetchData(4, 2025, $placements['Pabrik 4'])['data'];
        $Pabrik5_WP = $this->fetchData(4, 2025, $placements['Pabrik 5'])['data'];
        $YEV_SUNRA_WP = $this->fetchData(4, 2025, $placements['YEV SUNRA'])['data'];
        $STI_WP = $this->fetchData(4, 2025, $placements['STI'])['data'];
        $TDB_WP = $this->fetchData(4, 2025, $placements['TDB'])['data'];
        $EXIM_WP = $this->fetchData(4, 2025, $placements['EXIM'])['data'];
        return view('payroll_excel_detail_report_view', [

            // data report
            'month' => $this->month,
            'year' => $this->year,
            'last_month' => $last_month,
            'last_year' => $last_year,
            'results' => $results,
            'last_results' => $last_results,
            'selisih_results' => $selisih_results,
            'companies' => array_keys($companies),
            'total' => $total,
            'last_total' => $last_total,
            'total_selisih' => $total_selisih,
            'count' => $count,
            'last_count' => $last_count,

            'YAM' => $YAM,
            'YSM_CF' => $YSM_CF,
            // 'YSM_Wanto' => $YSM_Wanto,
            'YSM' => $YSM,
            'YIG' => $YIG,
            'YIG_CF' => $YIG_CF,
            'YCME' => $YCME,
            'YCME_CF' => $YCME_CF,
            'YCME_exim' => $YCME_exim,
            'YCME_XIN_XUN' => $YCME_XIN_XUN,
            'YCME_Pabrik1' => $YCME_Pabrik1,
            'YCME_Pabrik1_CF' => $YCME_Pabrik1_CF,
            'YCME_Pabrik2' => $YCME_Pabrik2,
            'YCME_Pabrik2_CF' => $YCME_Pabrik2_CF,
            'YCME_Pabrik3' => $YCME_Pabrik3,
            'YCME_Pabrik3_CF' => $YCME_Pabrik3_CF,
            'YCME_Pabrik4' => $YCME_Pabrik4,
            'YCME_Pabrik4_CF' => $YCME_Pabrik4_CF,
            'YCME_Sunra' => $YCME_Sunra,
            'YCME_Sunra_CF' => $YCME_Sunra_CF,
            'YNE' => $YNE,
            'STI' => $STI,
            'placement_YAM' => $placement_YAM,
            'placement_YSM' => $placement_YSM,
            'placement_YSM_CF' => $placement_YSM_CF,
            'placement_YIG' => $placement_YIG,
            'placement_YIG_CF' => $placement_YIG_CF,
            'placement_YCME' => $placement_YCME,
            'placement_YCME_CF' => $placement_YCME_CF,
            'placement_pabrik1' => $placement_pabrik1,
            'placement_pabrik1_CF' => $placement_pabrik1_CF,
            'placement_pabrik2' => $placement_pabrik2,
            'placement_pabrik2_CF' => $placement_pabrik2_CF,
            'placement_pabrik3' => $placement_pabrik3,
            'placement_pabrik3_CF' => $placement_pabrik3_CF,
            'placement_pabrik4' => $placement_pabrik4,
            'placement_pabrik4_CF' => $placement_pabrik4_CF,
            'placement_SUNRA' => $placement_SUNRA,
            'placement_SUNRA_CF' => $placement_SUNRA_CF,
            'placement_Pabrik5_YNE' => $placement_Pabrik5_YNE,
            'placement_STI' => $placement_STI,
            'placement_TDB' => $placement_TDB,
            'YAM_WP' => $YAM_WP,
            'YSM_WP' => $YSM_WP,
            'YIG_WP' => $YIG_WP,
            'Pabrik1_WP' => $Pabrik1_WP,
            'Pabrik2_WP' => $Pabrik2_WP,
            'Pabrik3_WP' => $Pabrik3_WP,
            'Pabrik4_WP' => $Pabrik4_WP,
            'Pabrik5_WP' => $Pabrik5_WP,
            'YEV_SUNRA_WP' => $YEV_SUNRA_WP,
            'STI_WP' => $STI_WP,
            'TDB_WP' => $TDB_WP,
            'placement_YCME_XIN_XUN' => $placement_YCME_XIN_XUN,
            'placement_YCME_EXIM' => $placement_YCME_EXIM,
            'EXIM_WP' => $EXIM_WP,


        ]);
    }

    public function columnFormats(): array
    {
        return [
            // 'C' => NumberFormat::FORMAT_TEXT,
            // 'D' => "0",
            'A' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'B' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'P' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'Q' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'R' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'S' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'T' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'U' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'V' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'W' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'X' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'Y' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,

            'AA' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AB' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AC' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AD' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AE' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AF' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AG' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AH' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AI' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AJ' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AK' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AL' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AM' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AN' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AO' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            // 'AS' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AP' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AQ' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AT' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AU' => "0",
            'AV' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            2    => ['font' => ['bold' => true]],
            // Styling a specific cell by coordinate.

            // Styling an entire column.
            2  => ['font' => ['size' => 15]],
            // 2 => ['font' => ['italic' => true]],
            3  => ['font' => ['size' => 12]],


        ];
    }
}
