<?php

namespace App\Exports;

use App\Models\Karyawan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class KaryawanExportLengkap implements FromView,  ShouldAutoSize, WithColumnFormatting, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $selected_placement, $selected_company, $selectStatus, $search_etnis;

    public function __construct($selected_placement, $selected_company, $selected_department, $selectStatus, $search_etnis)
    {
        $this->selected_placement = $selected_placement;
        $this->selected_company = $selected_company;
        $this->selected_department = $selected_department;
        $this->selectStatus = $selectStatus;
        $this->search_etnis = $search_etnis;
    }

    public function view(): View
    {

        if ($this->selectStatus == 1) {
            $statuses = ['PKWT', 'PKWTT', 'Dirumahkan'];
        } elseif ($this->selectStatus == 2) {
            $statuses = ['Blacklist', 'Resigned'];
        } else {
            $statuses = ['PKWT', 'PKWTT', 'Dirumahkan', 'Resigned', 'Blacklist'];
        }
        $data = Karyawan::whereIn('status_karyawan', $statuses);

        if ($this->selected_placement) {
            $data = $data->where('placement_id', $this->selected_placement);
        }

        if ($this->selected_company) {
            $data = $data->where('company_id', $this->selected_company);
        }
        if ($this->selected_department) {
            $data = $data->where('department_id', $this->selected_department);
        }
        if ($this->search_etnis) {
            $data = $data->where('etnis', $this->search_etnis);
        }

        $data = $data->get();

        $placement = nama_placement($this->selected_placement);
        $company = nama_company($this->selected_company);
        $department = nama_department($this->selected_department);

        if ($placement || $company || $department || $this->search_etnis) {
            $header_text = 'Data Karyawan';
            if ($company) $header_text = $header_text . ' Company ' . $company;
            if ($placement) $header_text = $header_text . ' Directorate ' . $placement;
            if ($department) $header_text = $header_text . ' Department ' . $department;
            if ($this->search_etnis) $header_text = $header_text . ' Etnis ' . $this->search_etnis;
        } else {
            $header_text = 'Data Seluruh Karyawan';
        }

        return view('karyawan_excel_view_lengkap', [
            'data' => $data,
            'header_text' => $header_text
        ]);
    }




    public function columnFormats(): array
    {
        return [
            // 'C' => NumberFormat::FORMAT_TEXT,
            // 'D' => '0',

            // 'I' => NumberFormat::FORMAT_DATE_XLSX15,
            // 'L' => "0",
            // 'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            // 'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            // 'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            // 'P' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            // 'Q' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            // 'R' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            // 'S' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,

            // 'AB' => "0",
            // 'AB' => NumberFormat::FORMAT_TEXT,
            // 'AE' => "0",
            // 'AF' => "0",
            // 'AF' => NumberFormat::FORMAT_TEXT,

            'AH' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AI' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AJ' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AK' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AL' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AM' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'AN' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,





        ];
    }

    public function headings(): array
    {
        return [['Data Karyawan'], [
            'ID Karyawan',
            'Nama',
            'Company',
            'Placement',
            'Jabatan',
            'Status Karyawan',
            'Tanggal Bergabung',
            'Metode Penggajian',
            'Gaji Pokok',
            'Gaji Lembur',
            'Gaji BPJS',
        ]];
    }

    public function title(): string
    {
        return 'Data Karyawan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            '1' => ['font' => ['bold' => true]],
            '2' => ['font' => ['bold' => true]],
            '1' => ['font' => ['size' => 24]],
            // 'C' => ['text' => ['align' => 'center']],
        ];
        // atau bisa juga
        // $sheet->getStyle('1')->getFont()->setBold(true);
        // $sheet->getStyle('2')->getFont()->setBold(true);
    }
}
