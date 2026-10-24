<?php

namespace App\Exports;

use App\Models\Karyawan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class karyawanExportKhususBaru implements
    FromView,
    ShouldAutoSize,
    WithColumnFormatting,
    WithStyles
{
    protected $semuaData;
    protected $bulan;
    protected $tahun;

    public function __construct($semuaData, $bulan, $tahun)
    {
        $this->semuaData = $semuaData;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function view(): View
    {
        $query = Karyawan::query();

        $query->where(function ($q) {
            // Semua karyawan aktif selalu ditampilkan.
            $q->whereIn('status_karyawan', [
                'PKWT',
                'PKWTT',
                'Dirumahkan',
            ]);

            if ($this->semuaData) {
                // Semua Resigned ditampilkan.
                $q->orWhere('status_karyawan', 'Resigned');
            } else {
                // Resigned mulai dari bulan dan tahun pilihan.
                $tanggalMulai = Carbon::create(
                    (int) $this->tahun,
                    (int) $this->bulan,
                    1
                )->startOfMonth();

                $q->orWhere(function ($resigned) use ($tanggalMulai) {
                    $resigned
                        ->where('status_karyawan', 'Resigned')
                        ->whereNotNull('tanggal_resigned')
                        ->where(
                            'tanggal_resigned',
                            '>=',
                            $tanggalMulai->toDateString()
                        );
                });
            }
        });

        // Blacklist otomatis tidak ikut karena tidak termasuk
        // dalam status yang diperbolehkan di atas.
        $data = $query->get();
        // Tambahkan tanda petik satu di depan nomor rekening.
        foreach ($data as $karyawan) {
            if (
                $karyawan->nomor_rekening !== null &&
                $karyawan->nomor_rekening !== ''
            ) {
                $karyawan->nomor_rekening =
                    "'" . $karyawan->nomor_rekening;
            }
        }

        $header_text = $this->semuaData
            ? 'Data Seluruh Karyawan'
            : 'Data Karyawan Mulai ' .
            Carbon::create(
                (int) $this->tahun,
                (int) $this->bulan,
                1
            )->translatedFormat('F Y');

        return view('karyawan_excel_view_khusus_baru', [
            'data' => $data,
            'header_text' => $header_text,
        ]);
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => '0',
            'Q' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 24,
                ],
            ],
            2 => [
                'font' => [
                    'bold' => true,
                ],
            ],
        ];
    }
}
