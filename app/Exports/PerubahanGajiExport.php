<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Karyawan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

// class PerubahanGaji implements FromCollection
class PerubahanGajiExport implements FromView,  ShouldAutoSize, WithColumnFormatting, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    // public function collection() {}

    protected $etnis, $year;
    public function __construct($etnis, $year)
    {
        $this->etnis = $etnis;
        $this->year = $year;
    }

    public function view(): View
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


        $header_text = "Gaji Karyawan Etnis " . $this->etnis . "Tahun " . $this->year;

        return view('perubahan_gaji_view', [
            'karyawans' => $karyawans,
            'header_text' => $header_text,

        ]);
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
    public function columnFormats(): array
    {
        return [
            // 'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,
            'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED,

        ];
    }
}
