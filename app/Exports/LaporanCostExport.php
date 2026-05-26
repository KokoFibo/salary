<?php

namespace App\Exports;

use App\Models\Payroll;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class LaporanCostExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }
    public function columnFormats(): array
    {
        return [

            // Bruto / Thn
            'G' => '#,##0',

            // Lembur
            'H' => '#,##0',

            // Shift Malam
            'I' => '#,##0',

            // Bonus
            'J' => '#,##0',

            // Potongan
            'K' => '#,##0',

            // JHT Karyawan
            'L' => '#,##0',

            // JP Karyawan
            'M' => '#,##0',

            // BPJS KS Karyawan
            'N' => '#,##0',

            // JHT Company
            'O' => '#,##0',

            // JP Company
            'P' => '#,##0',

            // JKK Company
            'Q' => '#,##0',

            // JKM Company
            'R' => '#,##0',

            // BPJS KS Company
            'S' => '#,##0',

            // PPh21 Jan-Nov
            'T' => '#,##0',

            // PPh21
            'U' => '#,##0',

            // Gaji Dibayarkan
            'V' => '#,##0',

            // TOTAL COST
            'W' => '#,##0',
        ];
    }

    public function collection()
    {
        $year = $this->year;

        $payrolls = Payroll::select(
            'payrolls.id_karyawan',
            'payrolls.nama',
            'payrolls.company_id',
            'payrolls.placement_id',
            'payrolls.department_id',
            'payrolls.jabatan_id',

            DB::raw("
                SUM(
                    CASE 
                        WHEN karyawans.etnis = 'China'
                            THEN payrolls.gaji_pokok
                        ELSE payrolls.subtotal
                    END
                )
                -
                SUM(
                    COALESCE(payrolls.gaji_lembur, 0)
                    * (
                        COALESCE(payrolls.jam_lembur, 0)
                        + COALESCE(payrolls.jam_lembur_libur, 0)
                    )
                )
                AS bruto_thn
            "),

            DB::raw("
                SUM(
                    payrolls.gaji_lembur
                    * (
                        COALESCE(payrolls.jam_lembur, 0)
                        + COALESCE(payrolls.jam_lembur_libur, 0)
                    )
                ) AS total_lembur
            "),

            DB::raw('SUM(payrolls.tambahan_shift_malam) AS shift_malam'),

            DB::raw("
                (
                    SELECT SUM(
                        COALESCE(bp.uang_makan, 0)
                        + COALESCE(bp.bonus_lain, 0)
                    )
                    FROM bonuspotongans bp
                    WHERE bp.user_id = payrolls.id_karyawan
                    AND YEAR(bp.tanggal) = {$year}
                ) AS bonus
            "),

            DB::raw("
                (
                    SELECT SUM(
                        COALESCE(bp.baju_esd, 0)
                        + COALESCE(bp.gelas, 0)
                        + COALESCE(bp.sandal, 0)
                        + COALESCE(bp.seragam, 0)
                        + COALESCE(bp.sport_bra, 0)
                        + COALESCE(bp.hijab_instan, 0)
                        + COALESCE(bp.id_card_hilang, 0)
                        + COALESCE(bp.masker_hijau, 0)
                        + COALESCE(bp.potongan_lain, 0)
                    )
                    FROM bonuspotongans bp
                    WHERE bp.user_id = payrolls.id_karyawan
                    AND YEAR(bp.tanggal) = {$year}
                )
                +
                (
                    SUM(COALESCE(payrolls.denda_lupa_absen, 0))
                    + SUM(COALESCE(payrolls.denda_resigned, 0))
                    + SUM(COALESCE(payrolls.other_deduction, 0))
                )
                AS potongan
            "),

            DB::raw('SUM(payrolls.jht) AS jht_karyawan'),
            DB::raw('SUM(payrolls.jp) AS jp_karyawan'),
            DB::raw('SUM(payrolls.kesehatan) AS bpjs_ks_karyawan'),

            DB::raw('SUM(payrolls.gaji_bpjs * 3.7 / 100) AS jht_company'),
            DB::raw('SUM(LEAST(payrolls.gaji_bpjs, 10547400) * 2 / 100) AS jp_company'),

            DB::raw('SUM(payrolls.gaji_bpjs) AS gaji_bpjs_thn'),
            DB::raw('MAX(payrolls.jkk) AS jkk_flag'),
            DB::raw('MAX(payrolls.jkm) AS jkm_flag'),

            DB::raw('SUM(payrolls.kesehatan * 4) AS bpjs_ks_company'),
            DB::raw('SUM(payrolls.pph21) AS pph21'),

            DB::raw("
                SUM(
                    CASE 
                        WHEN MONTH(payrolls.date) BETWEEN 1 AND 11 
                        THEN payrolls.pph21 
                        ELSE 0 
                    END
                ) AS pph21_jan_nov
            "),

            DB::raw('SUM(payrolls.total) AS gaji_dibayarkan'),

            DB::raw("
                SUM(
                    payrolls.total
                    + (payrolls.gaji_bpjs * 3.7 / 100)
                    + (LEAST(payrolls.gaji_bpjs, 10547400) * 2 / 100)
                    + payrolls.jkk
                    + payrolls.jkm
                    + payrolls.kesehatan
                ) AS total_cost
            ")
        )
            ->join('karyawans', 'karyawans.id_karyawan', '=', 'payrolls.id_karyawan')
            ->whereYear('payrolls.date', $year)
            ->groupBy('payrolls.id_karyawan')
            ->orderBy('payrolls.id_karyawan', 'asc')
            ->get();

        return $payrolls->map(function ($row) {

            $jkk_company = $row->jkk_flag == 1
                ? ($row->gaji_bpjs_thn * 0.24) / 100
                : 0;

            $jkm_company = $row->jkm_flag == 1
                ? ($row->gaji_bpjs_thn * 0.3) / 100
                : 0;

            $row->company    = nama_company($row->company_id);
            $row->placement  = nama_placement($row->placement_id);
            $row->department = nama_department($row->department_id);
            $row->jabatan    = nama_jabatan($row->jabatan_id);

            $row->jkk_company = $jkk_company;
            $row->jkm_company = $jkm_company;

            return $row;
        });
    }

    public function headings(): array
    {
        return [
            'Nama',
            'No ID',
            'Company',
            'Directorate',
            'Dept',
            'Jabatan',
            'Bruto / Thn',
            'Lembur',
            'Shift Malam',
            'Bonus',
            'Potongan',
            'JHT Karyawan',
            'JP Karyawan',
            'BPJS KS Karyawan',
            'JHT Company',
            'JP Company',
            'JKK Company',
            'JKM Company',
            'BPJS KS Company',
            'PPh21 (Jan-Nov)',
            'PPh21',
            'Gaji Dibayarkan',
            'TOTAL COST',
        ];
    }

    public function map($row): array
    {
        return [
            $row->nama,
            $row->id_karyawan,
            $row->company,
            $row->placement,
            $row->department,
            $row->jabatan,
            $row->bruto_thn,
            $row->total_lembur,
            $row->shift_malam,
            $row->bonus,
            $row->potongan,
            $row->jht_karyawan,
            $row->jp_karyawan,
            $row->bpjs_ks_karyawan,
            $row->jht_company,
            $row->jp_company,
            $row->jkk_company,
            $row->jkm_company,
            $row->bpjs_ks_company,
            $row->pph21_jan_nov,
            $row->pph21,
            $row->gaji_dibayarkan,
            $row->total_cost,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }
}
