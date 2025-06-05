<?php

use Carbon\Carbon;
use App\Models\Payroll;
use App\Models\Karyawan;
use App\Models\Bonuspotongan;
use App\Models\Jamkerjaid;
use App\Models\Liburnasional;
use App\Models\Lock;
use App\Models\Yfrekappresensi;
//ok 1

//Ori

function build_payroll_os_new($month, $year)
{
    // $lock = Lock::find(1);
    // $lock->rebuild_done = 2;
    // $lock->save();

    $libur = Liburnasional::whereMonth('tanggal_mulai_hari_libur', $month)->whereYear('tanggal_mulai_hari_libur', $year)->orderBy('tanggal_mulai_hari_libur', 'asc')->get('tanggal_mulai_hari_libur');
    $total_n_hari_kerja = getTotalWorkingDays($year, $month);
    $startOfMonth = Carbon::parse($year . '-' . $month . '-01');
    $endOfMonth = $startOfMonth->copy()->endOfMonth();
    $cx = 0;
    // isi ini dengan false jika mau langsung
    $pass = true;
    delete_failed_jobs();


    $jumlah_libur_nasional = jumlah_libur_nasional($month, $year);



    // $jamKerjaKosong = Jamkerjaid::count();
    $adaPresensi = Yfrekappresensi::whereMonth('date', $month)
        ->whereYear('date', $year)
        ->count();
    // if ($jamKerjaKosong == null || $adaPresensi == null) {
    if ($adaPresensi == null) {
        return 0;
        clear_locks();
        // $dispatch('error', message: 'Data Presensi Masih Kosong');
    }

    // AMBIL DATA TERAKHIR DARI REKAP PRESENSI PADA BULAN YBS
    $last_data_date = Yfrekappresensi::query()
        ->whereMonth('date', $month)
        ->whereYear('date', $year)
        ->orderBy('date', 'desc')
        ->first();
    //     delete jamkerjaid yg akan di build
    if ($pass) {
        Jamkerjaid::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->delete();
        delete_failed_jobs();
    }

    // dd('ok1 sampai sini');

    $jumlah_jam_terlambat = null;
    $jumlah_menit_lembur = null;
    $dt_name = null;
    $dt_date = null;
    $dt_karyawan_id = null;
    $late = null;
    $late1 = null;
    $late2 = null;
    $late3 = null;
    $late4 = null;
    $late5 = null;

    $filterArray = Yfrekappresensi::whereMonth('date', $month)
        ->whereYear('date', $year)
        // ->where('status')
        ->pluck('user_id')
        ->unique();

    if ($filterArray == null) {
        return 0;
    }



    // disini mulai prosesnya

    //     foreach ($filteredData as $data) {
    // proses ini yg lama1

    // dd('first step done');
    // echo 'rekap done';

    // ok2 perhitungan payroll

    // $datas = Jamkerjaid::with('karyawan', 'yfrekappresensi')
    //     ->whereBetween('date', [Carbon::parse($year . '-' . $month . '-01'), Carbon::parse($year . '-' . $month . '-01')->endOfMonth()])
    //     ->get();

    // if ($datas->isEmpty()) {
    //     return 0;
    // }

    $subtotal = 0;
    $denda_noscan = 0;


    Payroll::whereMonth('date', $month)
        ->whereYear('date', $year)
        ->delete();

    $rekaps_id = Yfrekappresensi::whereYear('date', $year)->whereMonth('date', $month)->pluck('user_id')->unique();
    $data_karyawan_tka = Karyawan::where('etnis', 'China')
        ->whereNotIn('status_karyawan', ['Blacklist', 'Resigned'])
        ->pluck('id_karyawan')
        ->unique();
    $id_exclusive = $data_karyawan_tka
        ->diff($rekaps_id);

    $id_exclusive = $id_exclusive
        ->push(2) // Eddy Chan
        ->push(4) // Rudy Chan
        ->push(6435) // Wanto
        ->unique()
        ->sort()
        ->values();


    // Gabungkan dua collection dan hapus duplikat
    $combined_ids = $rekaps_id->merge($data_karyawan_tka)->unique();

    // Jika butuh sebagai array:
    $combined_array = $combined_ids->sort()->values()->all();

    foreach ($rekaps_id as $rekap_id) {

        $data_sebulan_user = Yfrekappresensi::join('karyawans', 'yfrekappresensis.user_id', '=', 'karyawans.id_karyawan')
            ->select(
                'yfrekappresensis.*',
                'karyawans.*',
                // 'karyawans.id_karyawan as id_karyawan',
                // 'karyawans.nama as nama',
                // 'karyawans.gaji_pokok as gaji_pokok',
                // 'karyawans.gaji_bpjs as gaji_bpjs',
                // 'karyawans.metode_penggajian as metode_penggajian'
            )
            ->whereMonth('yfrekappresensis.date', $month)
            ->whereYear('yfrekappresensis.date', $year)
            ->where('user_id', $rekap_id)
            ->get();


        $total_jam_kerja = 0;
        $total_jam_lembur = 0;
        $total_hari_kerja = 0;
        $late = 0;
        $no_scan = 0;
        $total_tambahan_shift_malam = 0;
        $tambahan_jam_shift_malam = 0;
        $jam_kerja_libur = 0;

        foreach ($data_sebulan_user as $data) {
            $total_jam_kerja += $data->total_jam_kerja;
            $total_jam_lembur += $data->total_jam_lembur;
            $total_hari_kerja++;
            $late += $data->late;
            $tambahan_jam_shift_malam += $data->shift_malam;
            if ($data->no_scan_history)  $no_scan++;

            // tambahan_shift_malam
            if ($data->shift == 'Malam') {
                if (is_saturday($data->date)) {
                    if ($total_jam_kerja >= 6) {
                        $tambahan_shift_malam = 1;
                    }
                } elseif (is_sunday($data->date)) {
                    if ($total_jam_kerja >= 16) {
                        // $jam_lembur = $jam_lembur + 2;
                        $tambahan_shift_malam = 1;
                    }
                } else {
                    if ($total_jam_kerja >= 8) {
                        // $jam_lembur = $jam_lembur + 1;
                        $tambahan_shift_malam = 1;
                    }
                }
                $total_tambahan_shift_malam += $tambahan_shift_malam;
            }
            // end tambahan_shift_malam
            // jam kerja libur
            if ((is_sunday($data->date) || is_libur_nasional($data->date)) && trim($data->karyawan->metode_penggajian) == 'Perbulan') {
                $total_hari_kerja--;
                $jam_kerja_libur += $data->total_jam_kerja;
            }
        } // ini loop per data karyawan selama sebulan

        // if ($data->user_id == 1070) dd($data->user_id, $total_hari_kerja++);
        if (trim($data->karyawan->metode_penggajian) == 'Perjam') {
            $subtotal = $total_jam_kerja * ($data->karyawan->gaji_pokok / 198) + $total_jam_lembur * $data->karyawan->gaji_overtime;
            // if ($data->user_id == 1055) dd($data->user_id, $total_jam_lembur, $total_jam_kerja, $subtotal);
        } else {
            $gaji_harian = $data->karyawan->gaji_pokok / $total_n_hari_kerja;
            if ($data->karyawan->etnis == 'China') {
                $subtotal = $data->karyawan->gaji_pokok;
            } else {
                $subtotal = $gaji_harian * $total_hari_kerja +  $data->total_jam_lembur * $data->karyawan->gaji_overtime;
            }
        }

        //   hitung BPJS

        if ($data->karyawan->potongan_JP == 1) {
            if ($data->karyawan->gaji_bpjs <= 10547400) {
                $jp = $data->karyawan->gaji_bpjs * 0.01;
            } else {
                $jp = 10547400 * 0.01;
            }
        } else {
            $jp = 0;
        }


        if ($data->karyawan->potongan_JHT == 1) {
            $jht = $data->karyawan->gaji_bpjs * 0.02;
        } else {
            $jht = 0;
        }

        if ($data->karyawan->potongan_kesehatan == 1) {
            $data_gaji_bpjs = 0;
            if ($data->karyawan->gaji_bpjs >= 12000000) $data_gaji_bpjs = 12000000;
            else $data_gaji_bpjs = $data->karyawan->gaji_bpjs;

            $kesehatan = $data_gaji_bpjs * 0.01;
        } else {
            $kesehatan = 0;
        }

        if ($data->karyawan->tanggungan >= 1) {
            $tanggungan = $data->karyawan->tanggungan * $data->karyawan->gaji_bpjs * 0.01;
        } else {
            $tanggungan = 0;
        }

        if ($data->karyawan->potongan_JKK == 1) {
            $jkk = 1;
        } else {
            $jkk = 0;
        }
        if ($data->karyawan->potongan_JKM == 1) {
            $jkm = 1;
        } else {
            $jkm = 0;
        }

        // end of bpjs

        // denda lupa absen

        if ($no_scan == null) {
            $denda_lupa_absen = 0;
        } else {
            if ($no_scan <= 3) {
                $denda_lupa_absen = 0;
            } else {
                $denda_lupa_absen = ($no_scan - 3) * ($data->karyawan->gaji_pokok / 198);
            }
        }




        // Gaji Libur
        $gaji_libur = 0;
        if ($data->karyawan->etnis !=  'China') {
            $gaji_libur = ($jam_kerja_libur * ($data->karyawan->gaji_pokok / 198));
            $denda_lupa_absen = 0; // TKA tidak ada denda absen

        }


        $total = $subtotal;
        $tambahan_jam_shift_malam = $total_tambahan_shift_malam * $data->karyawan->gaji_overtime;
        $tambahan_shift_malam = $total_tambahan_shift_malam * $data->karyawan->gaji_overtime;
        if ($data->karyawan->jabatan_id == 17) {
            $tambahan_shift_malam = $total_tambahan_shift_malam * $data->karyawan->gaji_shift_malam_satpam;
        }

        // gaji bulan ini
        if ($data->karyawan->metode_penggajian == "Perjam") {
            $gaji_bulan_ini = total_gaji_perjam($data->karyawan->gaji_pokok, $total_jam_kerja);
        } else {
            $gaji_bulan_ini = total_gaji_bulanan(
                $data->karyawan->gaji_pokok,
                $total_hari_kerja,
                $total_n_hari_kerja,
                $jumlah_libur_nasional,
                $data->date,
                $data->karyawan->id_karyawan,
                $data->karyawan->status_karyawan
            );
        }
        // if ($data->karyawan->id_karyawan == 1068) dd(
        //     $gaji_bulan_ini,
        //     $data->karyawan->gaji_pokok,
        //     $total_hari_kerja,
        //     $total_n_hari_kerja,
        //     $jumlah_libur_nasional,
        //     $data->date,
        //     $data->karyawan->id_karyawan,
        //     $data->karyawan->status_karyawan
        // );
        // gaji bpjs adjust
        if ($data->karyawan->gaji_pokok != 0) {
            // $gaji_bpjs_adjust = $data->karyawan->gaji_bpjs * $total_gaji_sebelum_tax / $data->karyawan->gaji_pokok;
            $gaji_bpjs_adjust = $data->karyawan->gaji_bpjs * $gaji_bulan_ini / $data->karyawan->gaji_pokok;
        } else {
            // Handle the case where gaji_pokok is zero (e.g., log an error or assign a default value)
            $gaji_bpjs_adjust = 0; // or another fallback value
            error_log("Division by zero error: gaji_pokok is zero for karyawan ID: " . $data->karyawan->id);
        }
        // JKK Company
        if ($data->karyawan->potongan_JKK) {
            $jkk_company = ($data->karyawan->gaji_bpjs * 0.24) / 100;
            if ($data->karyawan->company_id == 102) {
                $jkk_company = ($data->karyawan->gaji_bpjs * 0.89) / 100;
            }
        } else {
            $jkk_company = 0;
        }

        // JKM Company
        if ($data->karyawan->potongan_JKM) {
            $jkm_company = ($data->karyawan->gaji_bpjs * 0.3) / 100;
        } else {
            $jkm_company = 0;
        }

        // Kesehatan Company
        if ($data->karyawan->potongan_kesehatan == 1) {
            $data_gaji_bpjs = 0;
            if ($data->karyawan->gaji_bpjs >= 12000000) $data_gaji_bpjs = 12000000;
            else $data_gaji_bpjs = $data->karyawan->gaji_bpjs;
            $kesehatan = $data_gaji_bpjs * 0.01;
            $kesehatan_company = ($data_gaji_bpjs * 4) / 100;
        } else {
            $kesehatan = 0;
            $kesehatan_company = 0;
        }

        // total gaji lembur
        $total_gaji_lembur = $data->jumlah_menit_lembur * $data->karyawan->gaji_overtime;

        // bonus dan potongan
        $bonus_potongan = Bonuspotongan::whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->where('user_id', $data->user_id)
            ->first();
        if ($bonus_potongan) {
            $all_bonus = ($bonus_potongan->uang_makan ?? 0) + ($bonus_potongan->bonus_lain ?? 0);
            $all_potongan =
                ($bonus_potongan->baju_esd ?? 0) + ($bonus_potongan->gelas ?? 0) + ($bonus_potongan->sandal ?? 0) +
                ($bonus_potongan->seragam ?? 0) + ($bonus_potongan->sport_bra ?? 0) + ($bonus_potongan->hijab_instan ?? 0) +
                ($bonus_potongan->id_card_hilang ?? 0) + ($bonus_potongan->masker_hijau ?? 0) + ($d->potongan_lain ?? 0);
        } else {
            $all_bonus = 0;
            $all_potongan = 0;
        }


        // total BPJS
        $total_tax =
            $gaji_bpjs_adjust +
            $jkk_company +
            $jkm_company +
            $kesehatan_company +
            $total_gaji_lembur +
            $gaji_libur +
            $tambahan_shift_malam +
            $all_bonus;

        // total bonus karyawan
        $total_bonus_dari_karyawan = $data->karyawan->bonus + $data->karyawan->tunjangan_jabatan + $data->karyawan->tunjangan_bahasa + $data->karyawan->tunjangan_skill + $data->karyawan->tunjangan_lembur_sabtu + $data->karyawan->tunjangan_lama_kerja;

        // pph21
        $pph21 = hitung_pph21(
            $data->karyawan->gaji_bpjs,
            // $gaji_bpjs_adjust,
            $data->karyawan->ptkp,
            $data->karyawan->potongan_JHT,
            $data->karyawan->potongan_JP,
            $data->karyawan->potongan_JKK,
            $data->karyawan->potongan_JKM,
            $data->karyawan->potongan_kesehatan,
            $total_gaji_lembur,
            $gaji_libur,
            $total_bonus_dari_karyawan, // apakah benar ini?
            $tambahan_shift_malam,
            $data->karyawan->company_id
        );
        // if ($data->karyawan->id_karyawan == 1034) {
        //     dd(
        //         $data->karyawan->gaji_bpjs,
        //         $data->karyawan->ptkp,
        //         $data->karyawan->potongan_JHT,
        //         $data->karyawan->potongan_JP,
        //         $data->karyawan->potongan_JKK,
        //         $data->karyawan->potongan_JKM,
        //         $data->karyawan->potongan_kesehatan,
        //         $total_gaji_lembur,
        //         $gaji_libur,
        //         $total_bonus_dari_karyawan,
        //         $tambahan_shift_malam,
        //         $data->karyawan->company_id
        //     );
        // }

        // function hitung_pph21(
        //     $gaji_bpjs,
        //     $ptkp,
        //     $jht,
        //     $jp,
        //     $jkk,
        //     $jkm,
        //     $kesehatan,
        //     $total_gaji_lembur,
        //     $gaji_libur,
        //     $total_bonus_dari_karyawan,
        //     $tambahan_shift_malam,
        //     $company_id
        // )

        // Manfaat Libur
        if ($data->karyawan->status_karyawan == 'Resigned') {
            $manfaat_libur = manfaat_libur_resigned($month, $year, $libur, $data->user_id, $data->karyawan->tanggal_resigned);
        } else {
            $manfaat_libur = manfaat_libur($month, $year, $libur, $data->user_id, $data->karyawan->tanggal_bergabung);
            if ($manfaat_libur > $libur->count()) $manfaat_libur = $libur->count();
        }
        if ($data->karyawan->metode_penggajian == 'Perbulan') {
            $subtotal = $subtotal + $manfaat_libur * ($data->karyawan->gaji_pokok / $total_n_hari_kerja);
        }
        if ($data->karyawan->etnis == 'China') {
            $subtotal = $data->karyawan->gaji_pokok;
            $total_hari_kerja = $total_n_hari_kerja - $libur->count();
        }

        // if ($data->karyawan->id_karyawan == '1005') dd($all_bonus, $data->karyawan->id_karyawan);
        Payroll::create([
            'jp' => $jp,
            'jht' => $jht,
            'kesehatan' => $kesehatan,
            'tanggungan' => $tanggungan,
            'jkk' => $jkk,
            'jkm' => $jkm,
            'denda_lupa_absen' => $denda_lupa_absen,
            'gaji_libur' => $gaji_libur,

            // 'jamkerjaid_id' => $data->id,
            'nama' => $data->karyawan->nama,
            'id_karyawan' => $data->karyawan->id_karyawan,


            'jabatan_id' => $data->karyawan->jabatan_id,
            'company_id' => $data->karyawan->company_id,
            'placement_id' => $data->karyawan->placement_id,
            'department_id' => $data->karyawan->department_id,

            'status_karyawan' => $data->karyawan->status_karyawan,
            'metode_penggajian' => $data->karyawan->metode_penggajian,
            'nomor_rekening' => $data->karyawan->nomor_rekening,
            'nama_bank' => $data->karyawan->nama_bank,
            'gaji_pokok' => $data->karyawan->gaji_pokok,
            'gaji_lembur' => $data->karyawan->gaji_overtime,
            'gaji_bpjs' => $data->karyawan->gaji_bpjs,
            'ptkp' => $data->karyawan->ptkp,
            'libur_nasional' => $jumlah_libur_nasional,

            'hari_kerja' => $total_hari_kerja,
            'jam_kerja' => $total_jam_kerja,
            'jam_lembur' => $total_jam_lembur,
            'jumlah_jam_terlambat' => $late,
            'total_noscan' => $no_scan,
            'thr' => $data->karyawan->bonus,
            'tunjangan_jabatan' => $data->karyawan->tunjangan_jabatan,
            'tunjangan_bahasa' => $data->karyawan->tunjangan_bahasa,
            'tunjangan_skill' => $data->karyawan->tunjangan_skill,
            'tunjangan_lama_kerja' => $data->karyawan->tunjangan_lama_kerja,
            'tunjangan_lembur_sabtu' => $data->karyawan->tunjangan_lembur_sabtu,
            'iuran_air' => $data->karyawan->iuran_air,
            'iuran_locker' => $data->karyawan->iuran_locker,
            'tambahan_jam_shift_malam' => $total_tambahan_shift_malam,
            'tambahan_shift_malam' => $tambahan_shift_malam,
            'subtotal' => $subtotal,
            'date' => buatTanggal($data->date),
            'pph21' => $pph21,
            'bonus1x' => $all_bonus,
            'potongan1x' => $all_potongan,
            'total' => $total,
            'total_bpjs' => $total_tax,
            'bpjs_adjustment' => $gaji_bpjs_adjust,
            'gaji_bulan_ini' => $gaji_bulan_ini

        ]);
    } // ini loop untuk data setiap karyawan
    // loop utk karyawan exclusive
    dd('stop sini dulu');
    foreach ($id_exclusive as $rekap_id) {
    }
}
