<?php

namespace App\Exports;

use App\Models\Karyawan;
use App\Models\Payroll;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExcelResignedBlacklist implements FromView,  ShouldAutoSize, WithColumnFormatting, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;

    protected $data;

    protected $selectStatus;
    public function __construct($selectStatus)
    {
        $this->selectStatus = $selectStatus;
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
        $nama_status = "";
        if ($this->selectStatus == 2) {
            $data = Karyawan::where('status_karyawan', 'Resigned')
                ->orderBy('tanggal_resigned', 'desc')
                ->get();
            $nama_status = "Resigned";
        }
        if ($this->selectStatus == 3) {
            $data = Karyawan::where('status_karyawan', 'Blacklist')
                ->orderBy('tanggal_blacklist', 'desc')
                ->get();
            $nama_status = "Blacklist";
        }

        $header_text = 'Data Seluruh Karyawan ' . $nama_status;

        return view('karyawan_excel_resigned_blacklist_view', [
            'data' => $data,
            'header_text' => $header_text,
            'title' => $header_text,
            'nama_status' => $nama_status,
            'status' => $this->selectStatus,
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
            'AU' => "0",
            'AV' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AW' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AX' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AY' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AZ' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'BA' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'BB' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'BC' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED
        ];
    }
}
