<?php

namespace App\Exports;


use App\Models\Payroll;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PayrollGabunganExport implements FromView,  ShouldAutoSize, WithColumnFormatting, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;

    protected $data;

    protected $selected_company, $selected_placement, $selected_department, $status, $month, $year;
    public function __construct($selected_company, $selected_placement, $selected_department, $status, $month, $year)
    {
        $this->selected_company = $selected_company;
        $this->selected_placement = $selected_placement;
        $this->selected_department = $selected_department;
        $this->status = $status;
        $this->month = $month;
        $this->year = $year;
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


    public function view(): View
    {
        if ($this->status == 1) {
            $statuses = ['PKWT', 'PKWTT', 'Dirumahkan', 'Resigned'];
        } elseif ($this->status == 2) {
            $statuses = ['Blacklist'];
        } else {
            $statuses = ['PKWT', 'PKWTT', 'Dirumahkan', 'Resigned', 'Blacklist'];
        }

        if ($this->selected_company != 0) {
            $data = Payroll::whereIn('status_karyawan', $statuses)
                ->where('company_id', $this->selected_company)
                ->whereMonth('date', $this->month)
                ->whereYear('date', $this->year)
                ->orderBy('id_karyawan', 'asc')->get();
            $title = 'Payroll By Company Gabungan';
            $header_text = 'Perincian Payroll by Company ' . nama_company($this->selected_company) . ' ' . nama_bulan($this->month) . ' ' . $this->year;
        } else if ($this->selected_placement != 0) {
            $data = Payroll::whereIn('status_karyawan', $statuses)
                ->where('placement_id', $this->selected_placement)
                ->whereMonth('date', $this->month)
                ->whereYear('date', $this->year)
                ->orderBy('id_karyawan', 'asc')->get();
            $title = 'Payroll By Placement Gabungan';
            $header_text = 'Perincian Payroll by Placement ' . nama_placement($this->selected_placement) . ' ' . nama_bulan($this->month) . ' ' . $this->year;
        } else if ($this->selected_department != 0) {
            $data = Payroll::whereIn('status_karyawan', $statuses)
                ->where('department_id', $this->selected_department)
                ->whereMonth('date', $this->month)
                ->whereYear('date', $this->year)
                ->orderBy('id_karyawan', 'asc')->get();
            $title = 'Payroll By Department Gabungan';
            $header_text = 'Perincian Payroll by Department ' . nama_department($this->selected_department) . ' ' . nama_bulan($this->month) . ' ' . $this->year;
        } else {
            $data = Payroll::whereIn('status_karyawan', $statuses)
                ->whereMonth('date', $this->month)
                ->whereYear('date', $this->year)
                ->orderBy('id_karyawan', 'asc')->get();
            $title = 'Payroll All Gabungan';
            $header_text = 'Perincian Seluruh Payroll ' . nama_bulan($this->month) . ' ' . $this->year;
        }

        // if ($this->selected_company == 0) {
        //     $data = Payroll::whereIn('status_karyawan', $statuses)
        //         ->whereMonth('date', $this->month)
        //         ->whereYear('date', $this->year)
        //         ->orderBy('id_karyawan', 'asc')->get();
        // } else {
        //     $data = Payroll::whereIn('status_karyawan', $statuses)
        //         ->where('company_id', $this->selected_company)
        //         ->whereMonth('date', $this->month)
        //         ->whereYear('date', $this->year)
        //         ->orderBy('id_karyawan', 'asc')->get();
        // }

        $total_n_hari_kerja = getTotalWorkingDays($this->year, $this->month);
        $jumlah_libur_nasional = jumlah_libur_nasional($this->month, $this->year);

        return view('payroll_excel_view', [
            'title' => $title,
            'data' => $data,
            'header_text' => $header_text,
            'total_n_hari_kerja' => $total_n_hari_kerja,
            'jumlah_libur_nasional' => $jumlah_libur_nasional


        ]);
    }

    public function columnFormats(): array
    {
        return [
            // 'C' => NumberFormat::FORMAT_TEXT,
            'D' => "0",
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
            'AU' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AV' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AW' =>  "0",
            'AX' => "0",
            'AY' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AZ' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'BA' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'BB' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'BC' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'BD' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'BE' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'BF' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED
        ];
    }
}
