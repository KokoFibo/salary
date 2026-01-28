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
            'payrolls.id_karyawan',
            'payrolls.nama',
            'payrolls.company_id',
            'payrolls.placement_id',
            'payrolls.department_id',
            'payrolls.jabatan_id',

            // =========================
            // BRUTO TAHUNAN
            // =========================
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

            // =========================
            // TOTAL LEMBUR
            // =========================
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

            // =========================
            // BONUS
            // =========================
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

            // =========================
            // POTONGAN
            // =========================
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

            // =========================
            // BPJS KARYAWAN
            // =========================
            DB::raw('SUM(payrolls.jht) AS jht_karyawan'),
            DB::raw('SUM(payrolls.jp) AS jp_karyawan'),
            // DB::raw('SUM(payrolls.bpjs_employee) AS bpjs_ks_karyawan'),
            DB::raw('SUM(payrolls.kesehatan) AS bpjs_ks_karyawan'),

            // =========================
            // BPJS COMPANY
            // =========================
            DB::raw('SUM(payrolls.gaji_bpjs * 3.7 / 100) AS jht_company'),
            DB::raw('SUM(LEAST(payrolls.gaji_bpjs, 10547400) * 2 / 100) AS jp_company'),

            DB::raw('payrolls.jkk AS jkk'),
            DB::raw('payrolls.jkm AS jkm'),

            DB::raw('SUM(payrolls.gaji_bpjs) AS gaji_bpjs_thn'),
            DB::raw('MAX(payrolls.jkk) AS jkk_flag'),
            DB::raw('MAX(payrolls.jkm) AS jkm_flag'),

            DB::raw('SUM(payrolls.kesehatan * 4) AS bpjs_ks_company'),
            DB::raw('SUM(payrolls.pph21) AS pph21'),

            // =========================
            // GAJI DIBAYARKAN
            // =========================
            DB::raw('SUM(payrolls.total) AS gaji_dibayarkan'),

            // =========================
            // TOTAL COST COMPANY
            // =========================
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

        $payrolls = $payrolls->map(function ($row) {

            $row->company    = nama_company($row->company_id);
            $row->placement  = nama_placement($row->placement_id);
            $row->department = nama_department($row->department_id);
            $row->jabatan    = nama_jabatan($row->jabatan_id);

            // $row->jkk = $row->jkk == 1 ? 'Yes' : 'No';
            // $row->jkm = $row->jkm == 1 ? 'Yes' : 'No';

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
