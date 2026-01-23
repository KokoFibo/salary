<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanCostController extends Controller
{

    public function index($year)
    {
        $year = 2025;
        $payrolls = Payroll::select(
            'id_karyawan',
            'nama',
            'company_id',
            'placement_id',
            'department_id',
            'jabatan_id',

            // DB::raw('SUM(subtotal)-SUM(gaji_lembur * (jam_lembur + jam_lembur_libur)) as bruto_thn'),
            // DB::raw('SUM(gaji_lembur * (jam_lembur + jam_lembur_libur)) as total_lembur'),

            DB::raw("
    SUM(subtotal) 
    - SUM(gaji_lembur * (COALESCE(jam_lembur,0) + COALESCE(jam_lembur_libur,0)))
    AS bruto_thn
"),
            DB::raw("
    SUM(gaji_lembur * (COALESCE(jam_lembur,0) + COALESCE(jam_lembur_libur,0)))
    AS total_lembur
"),


            DB::raw('SUM(tambahan_shift_malam) as shift_malam'),

            DB::raw('(
    SELECT SUM(
        COALESCE(bp.uang_makan,0) +
        COALESCE(bp.bonus_lain,0)
    )
    FROM bonuspotongans bp
    WHERE bp.user_id = payrolls.id_karyawan
    AND YEAR(bp.tanggal) = ' . $year . '
) as bonus'),


            // POTONGAN 1x (selain bonus)
            DB::raw('(
    SELECT SUM(
        COALESCE(bp.baju_esd,0) +
        COALESCE(bp.gelas,0) +
        COALESCE(bp.sandal,0) +
        COALESCE(bp.seragam,0) +
        COALESCE(bp.sport_bra,0) +
        COALESCE(bp.hijab_instan,0) +
        COALESCE(bp.id_card_hilang,0) +
        COALESCE(bp.masker_hijau,0) +
        COALESCE(bp.potongan_lain,0)
    )
    FROM bonuspotongans bp
    WHERE bp.user_id = payrolls.id_karyawan
    AND YEAR(bp.tanggal) = ' . $year . '
)
+ (
    SUM(COALESCE(denda_lupa_absen, 0)) +
    SUM(COALESCE(denda_resigned, 0)) +
    SUM(COALESCE(other_deduction, 0))
    )
    as potongan'),


            // BPJS Karyawan
            DB::raw('SUM(jht) as jht_karyawan'),
            DB::raw('SUM(jp) as jp_karyawan'),
            DB::raw('SUM(bpjs_employee) as bpjs_ks_karyawan'),

            // BPJS Company (RUMUS RESMI)
            DB::raw('SUM(gaji_bpjs * 3.7 / 100) as jht_company'),
            DB::raw('SUM(LEAST(gaji_bpjs, 10547400) * 2 / 100) as jp_company'),
            // DB::raw('SUM(jkk) as jkk_company'),
            // DB::raw('SUM(jkm) as jkm_company'),
            DB::raw('jkk as jkk'),
            DB::raw('jkm as jkm'),

            DB::raw('SUM(gaji_bpjs) as gaji_bpjs_thn'),
            DB::raw('MAX(jkk) as jkk_flag'),
            DB::raw('MAX(jkm) as jkm_flag'),

            DB::raw('SUM(kesehatan) as bpjs_ks_company'),

            DB::raw('SUM(pph21) as pph21'),

            // Gaji bersih ditransfer
            DB::raw('SUM(total) as gaji_dibayarkan'),

            // TOTAL COST COMPANY
            DB::raw('SUM(
                total
                + (gaji_bpjs * 3.7 / 100)
                + (LEAST(gaji_bpjs, 10547400) * 2 / 100)
                + jkk
                + jkm
                + kesehatan
            ) as total_cost')
        )
            ->whereYear('date', $year)
            ->groupBy(
                'id_karyawan',
                // 'nama',
                // 'company_id',
                // 'placement_id',
                // 'department_id',
                // 'jabatan_id'
            )
            ->orderBy('id_karyawan', 'asc')
            ->get();

        $payrolls = $payrolls->map(function ($row) {

            $row->company = nama_company($row->company_id);
            $row->placement = nama_placement($row->placement_id);
            $row->department = nama_department($row->department_id);
            $row->jabatan = nama_jabatan($row->jabatan_id);

            $row->jkk = $row->jkk == 1 ? 'Yes' : 'No';
            $row->jkm = $row->jkm == 1 ? 'Yes' : 'No';

            // JKK Company
            $row->jkk_company = $row->jkk_flag == 1
                ? ($row->gaji_bpjs_thn * 0.24) / 100
                : 0;

            // JKM Company
            $row->jkm_company = $row->jkm_flag == 1
                ? ($row->gaji_bpjs_thn * 0.3) / 100
                : 0;


            return $row;
        });

        return view('laporan-cost', compact('payrolls', 'year'));
    }
}
