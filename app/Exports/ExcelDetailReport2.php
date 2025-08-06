<?php

namespace App\Exports;

use DateTime;
use App\Models\Company;
use App\Models\Payroll;

use App\Models\Karyawan;
use App\Models\Placement;
use App\Models\Department;
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

class ExcelDetailReport2 implements FromView,  ShouldAutoSize, WithColumnFormatting, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */

    use Exportable;

    protected $data, $month, $year;

    public function view(): View
    {

        // Pembayaran PT
        $date = DateTime::createFromFormat('Y-n', "$this->year-$this->month");
        $date->modify('-1 month');
        $last_month = (int) $date->format('n');
        $last_year  = (int) $date->format('Y');
        $results = [];
        $last_results = [];
        $selisih_results = [];
        $companies = Company::all();



        $customOrder = [6, 102, 10, 9, 101, 103, 11, 3, 1, 109, 105, 2, 4, 13, 5, 104, 107, 106];

        $cases = "CASE";
        foreach ($customOrder as $index => $id) {
            $cases .= " WHEN id = {$id} THEN {$index}";
        }
        $cases .= " ELSE " . count($customOrder) . " END";

        $placements = Placement::query()
            ->orderByRaw($cases)
            ->orderBy('id') // untuk sisa data
            ->get();
        // dd($placements);

        // $placements = Placement::all();

        // saya mau diurut berdasarkan "id" =  6, 102, 10, 9, 101, 103, 11, 3, 1, 109, 105, 2, 4, 13, 5, 104, 107, 106 , sisanya diurut berdasarkan id ascending

        foreach ($companies as $company) {
            $total = Payroll::whereMonth('date', $this->month)
                ->whereYear('date', $this->year)
                ->where('company_id', $company->id)
                ->sum('total');

            $prf = Payroll::whereMonth('date', $this->month)
                ->whereYear('date', $this->year)
                ->where('company_id', $company->id)
                ->sum('prf');

            $core_cash = Payroll::whereMonth('date', $this->month)
                ->whereYear('date', $this->year)
                ->where('company_id', $company->id)
                ->sum('core_cash');

            $count = Payroll::whereMonth('date', $this->month)
                ->whereYear('date', $this->year)
                ->where('company_id', $company->id)
                ->count();

            $last_count = Payroll::whereMonth('date', $last_month)
                ->whereYear('date', $last_year)
                ->where('company_id', $company->id)
                ->count();
            $last_total = Payroll::whereMonth('date', $last_month)
                ->whereYear('date', $last_year)
                ->where('company_id', $company->id)
                ->sum('total');

            // ⛔ Skip jika semua nilai 0
            if ($total == 0 && $prf == 0 && $core_cash == 0 && $count == 0) {
                continue;
            }

            $results[$company->company_name] = [
                'total' => $total,
                'prf' => $prf,
                'core_cash' => $core_cash,
                'count' => $count,
                'last_count' => $last_count,
                'last_total' => $last_total,
                'selisih' => $last_total - $total,
            ];
        }
        ksort($results);

        $total_karyawan = 0;
        $count_gaji = 0;
        $prf_total = 0;
        $core_cash_total = 0;
        $total_count = 0;
        $total_last_count = 0;
        $total_last_gaji = 0;
        $total_last_selisih = 0;

        foreach ($results as $data) {
            $count_gaji += $data['total'];
            $total_karyawan += $data['count'];
            $prf_total += $data['prf'];
            $core_cash_total += $data['core_cash'];
            $total_last_count += $data['last_count'];
            $total_last_gaji += $data['last_total'];
            $total_last_selisih += $data['selisih'];
        }

        $result_company_placement = [];
        $total_tka = 0;
        $total_non_tka = 0;

        foreach ($companies as $company) {
            foreach ($placements as $placement) {
                $tka = Payroll::join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
                    ->where('payrolls.company_id', $company->id)
                    ->where('payrolls.placement_id', $placement->id)
                    ->whereMonth('payrolls.date', $this->month)
                    ->whereYear('payrolls.date', $this->year)
                    ->where('karyawans.etnis', 'China')
                    ->sum('payrolls.total');

                $non_tka = Payroll::join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
                    ->where('payrolls.company_id', $company->id)
                    ->where('payrolls.placement_id', $placement->id)
                    ->whereMonth('payrolls.date', $this->month)
                    ->whereYear('payrolls.date', $this->year)
                    ->where('karyawans.etnis', '!=', 'China')
                    ->sum('payrolls.total');

                if ($tka == 0 && $non_tka == 0) {
                    continue;
                }

                // Akumulasi total global
                $total_tka += $tka;
                $total_non_tka += $non_tka;

                $result_company_placement[] = [
                    'company' => $company->company_name,
                    'placement' => $placement->placement_name,
                    'tka_total' => $tka,
                    'non_tka_total' => $non_tka,
                ];
            }
        }

        // 🔠 Sort ASC berdasarkan company name dan placement
        usort($result_company_placement, function ($a, $b) {
            $cmpCompany = strcmp($a['company'], $b['company']);
            if ($cmpCompany === 0) {
                return strcmp($a['placement'], $b['placement']);
            }
            return $cmpCompany;
        });

        // Pembagian by Placement
        $result3 = [];
        $total_tka3 = 0;
        $total_non_tka3 = 0;
        $total_api3 = 0;

        foreach ($placements as $placement) {
            $tka = Payroll::join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
                ->where('payrolls.placement_id', $placement->id)
                ->whereMonth('payrolls.date', $this->month)
                ->whereYear('payrolls.date', $this->year)
                ->where('karyawans.etnis', 'China')
                ->sum('payrolls.total');

            $non_tka = Payroll::join('karyawans', 'payrolls.id_karyawan', '=', 'karyawans.id_karyawan')
                ->where('payrolls.placement_id', $placement->id)
                ->whereMonth('payrolls.date', $this->month)
                ->whereYear('payrolls.date', $this->year)
                ->where('karyawans.etnis', '!=', 'China')
                ->sum('payrolls.total');

            $api_data_array = $this->fetchData($this->month, $this->year, $placement->placement_name);

            $api_data = collect($api_data_array['data'])
                ->filter(fn($item) => is_numeric($item))
                ->sum();

            if ($tka == 0 && $non_tka == 0 && $api_data == 0) {
                continue;
            }

            $result3[] = [
                'placement' => $placement->placement_name, // asumsi ada field ini
                'tka_total' => $tka,
                'non_tka_total' => $non_tka,
                'api_total' => $api_data,
            ];

            // Akumulasi total keseluruhan
            $total_tka3 += $tka;
            $total_non_tka3 += $non_tka;
            $total_api3 += $api_data;
        }
        // Urutkan berdasarkan placement name
        // usort($result3, function ($a, $b) {
        //     return strcmp($a['placement'], $b['placement']);
        // });

        $customOrder = [6, 102, 10, 9, 101, 103, 11, 3, 1, 109, 105, 2, 4, 13, 5, 104, 107, 106];
        $orderMap = array_flip($customOrder);

        usort($result3, function ($a, $b) use ($orderMap) {
            // Tangani kasus jika key 'id' tidak tersedia
            $aId = is_array($a) && isset($a['id']) ? $a['id'] : null;
            $bId = is_array($b) && isset($b['id']) ? $b['id'] : null;

            $aOrder = $aId !== null && isset($orderMap[$aId]) ? $orderMap[$aId] : null;
            $bOrder = $bId !== null && isset($orderMap[$bId]) ? $orderMap[$bId] : null;

            if ($aOrder !== null && $bOrder !== null) {
                return $aOrder <=> $bOrder;
            }

            if ($aOrder !== null) return -1;
            if ($bOrder !== null) return 1;

            // Jika dua-duanya tidak ada di customOrder atau id-nya null
            return $aId <=> $bId;
        });













        return view('payroll_excel_detail_rpt_view2', [
            'month' => $this->month,
            'year' => $this->year,
            'results' => $results,
            'companies' => $companies,
            'placements' => $placements,



            'total_karyawan' => $total_karyawan,
            'count_gaji' => $count_gaji,
            'prf_total' => $prf_total,
            'core_cash_total' => $core_cash_total,
            'total_last_count' => $total_last_count,
            'total_last_gaji' => $total_last_gaji,
            'total_last_selisih' => $total_last_selisih,




            //  Pembagian PT Non OS
            'result_company_placement' => $result_company_placement,
            'total_tka' => $total_tka,
            'total_non_tka' => $total_non_tka,

            //Pembagian by Placement
            'result3' => $result3,
            'total_tka3' => $total_tka3,
            'total_non_tka3' => $total_non_tka3,
            'total_api3' => $total_api3,

            // Pembayaran PT
            'last_month' => $last_month,
            'last_year' => $last_year,
            'selisih_results' => $selisih_results,
            'last_results' => $last_results,

        ]);
    }

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function fetchData($month, $year, $placement_name)
    {
        $url = "http://payroll.yifang.co.id/api/os-placement-name/{$month}/{$year}/{$placement_name}";
        // $url = "http://payroll.yifang.co.id/api/os-placement/{$month}/{$year}/{$placement_id}";

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
