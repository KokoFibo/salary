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

function build_payroll($month, $year)
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
    if ($pass) {
        foreach ($filterArray as $data) {
            // $dataId = Yfrekappresensi::with('karyawan:id,jabatan,status_karyawan,metode_penggajian')

            //     ->where('user_id', $data)
            //     ->whereBetween('date', [Carbon::parse($year . '-' . $month . '-01'), Carbon::parse($year . '-' . $month . '-01')->endOfMonth()])
            //     ->orderBy('date', 'desc')
            //     ->get();



            $dataId = Yfrekappresensi::with('karyawan:id,jabatan_id,status_karyawan,metode_penggajian,placement_id,tanggal_blacklist')
                ->where('user_id', $data)
                ->where('date', '>=', $startOfMonth)
                ->where('date', '<=', $endOfMonth)
                ->orderBy('date', 'desc')
                ->get();

            // ambil data per user id
            $n_noscan = 0;
            $total_hari_kerja = 0;
            $total_jam_kerja = 0;
            $total_jam_lembur = 0;
            $langsungLembur = 0;
            $tambahan_shift_malam = 0;
            $total_keterlambatan = 0;
            $total_tambahan_shift_malam = 0;
            $jam_kerja_libur = 0;
            //loop ini utk 1 user selama 22 hari
            $get_placement = get_placement($dataId[0]->user_id);
            foreach ($dataId as $d) {
                if ($d->no_scan === null) {
                    $jam_lembur = 0;
                    $tambahan_shift_malam = 0;
                    $jam_kerja = hitung_jam_kerja($d->first_in, $d->first_out, $d->second_in, $d->second_out, $d->late, $d->shift, $d->date, $d->karyawan->jabatan_id, $get_placement);
                    $terlambat = late_check_jam_kerja_only($d->first_in, $d->first_out, $d->second_in, $d->second_out, $d->shift, $d->date, $d->karyawan->jabatan_id, $get_placement);
                    $langsungLembur = langsungLembur($d->second_out, $d->date, $d->shift, $d->karyawan->jabatan_id, $get_placement);

                    if (is_sunday($d->date)) {
                        $jam_lembur = hitungLembur($d->overtime_in, $d->overtime_out) / 60 * 2
                            + $langsungLembur * 2;
                    } else {
                        $jam_lembur = hitungLembur($d->overtime_in, $d->overtime_out) / 60 + $langsungLembur;
                    }

                    if ($d->shift == 'Malam') {
                        if (is_saturday($d->date)) {
                            if ($jam_kerja >= 6) {
                                $tambahan_shift_malam = 1;
                            }
                        } elseif (is_sunday($d->date)) {
                            if ($jam_kerja >= 16) {
                                // $jam_lembur = $jam_lembur + 2;
                                $tambahan_shift_malam = 1;
                            }
                        } else {
                            if ($jam_kerja >= 8) {
                                // $jam_lembur = $jam_lembur + 1;
                                $tambahan_shift_malam = 1;
                            }
                        }
                    }
                    if ($jam_lembur >= 9 && is_sunday($d->date) == false && $d->karyawan->jabatan_id != 22) {
                        $jam_lembur = 0;
                    }
                    // yig= 12, ysm= 13
                    if ($d->karyawan->placement_id == 12 || $d->karyawan->placement_id == 13 || $d->karyawan->jabatan_id == 17) {
                        if (is_friday($d->date)) {
                            $jam_kerja = 7.5;
                        } elseif (is_saturday($d->date)) {
                            $jam_kerja = 6;
                        } else {
                            $jam_kerja = 8;
                        }
                    }

                    if ($d->karyawan->jabatan_id == 17 && is_sunday($d->date)) {
                        $jam_kerja = hitung_jam_kerja($d->first_in, $d->first_out, $d->second_in, $d->second_out, $d->late, $d->shift, $d->date, $d->karyawan->jabatan_id, $get_placement);
                    }

                    if ($d->karyawan->jabatan_id == 17 && is_saturday($d->date)) {
                        // $jam_lembur = 0;
                    }



                    // Jika hari libur nasional
                    // 23 = translator
                    if ($d->karyawan->jabatan_id != 23) {
                        if (
                            is_libur_nasional($d->date) &&  !is_sunday($d->date)

                        ) {
                            $jam_kerja *= 2;
                            $jam_lembur *= 2;
                        }
                    } else {
                        if (is_sunday($d->date)) {
                            $jam_kerja /= 2;
                            $jam_lembur /= 2;
                        }
                    }

                    // khusus placement YAM yev yev... tgl 2024-04-07 dan 2024-04-09  
                    // $rule1 = ($d->date == '2024-04-07' || $d->date == '2024-04-09') &&  (substr($d->karyawan->placement, 0, 3) == "YEV" || $d->karyawan->placement == 'YAM');
                    // if ($rule1) {
                    //     $jam_kerja /= 2;
                    //     $jam_lembur /= 2;
                    // }


                    $total_hari_kerja++;

                    if ((is_sunday($d->date) || is_libur_nasional($d->date)) && trim($d->karyawan->metode_penggajian) == 'Perbulan') {
                        $total_hari_kerja--;
                        $jam_kerja_libur += $jam_kerja;
                    }

                    $total_jam_kerja = $total_jam_kerja + $jam_kerja;
                    $total_jam_lembur = $total_jam_lembur + $jam_lembur;
                    $total_keterlambatan = $total_keterlambatan + $terlambat;
                    $total_tambahan_shift_malam = $total_tambahan_shift_malam + $tambahan_shift_malam;
                }
                if ($d->no_scan_history != null) {
                    $n_noscan = $n_noscan + 1;
                }
            }
            if ($n_noscan == 0) {
                $n_noscan = null;
            }




            // $blacklist_condition = $d->karyawan && $d->karyawan->status_karyawan != 'Blacklist'
            //     || Carbon::parse($d->karyawan->tanggal_blacklist)->month > Carbon::parse($d->date)->month;










            if ($d->karyawan->status_karyawan != 'Blacklist') {
                $dataArr[] = [
                    'user_id' => $data,
                    'total_hari_kerja' => $total_hari_kerja,
                    'jumlah_jam_kerja' => $total_jam_kerja,
                    'jumlah_menit_lembur' => $total_jam_lembur,
                    'jumlah_jam_terlambat' => $total_keterlambatan,
                    'tambahan_jam_shift_malam' => $total_tambahan_shift_malam,
                    'jam_kerja_libur' => $jam_kerja_libur,


                    'total_noscan' => $n_noscan,
                    'karyawan_id' => $d->karyawan->id,
                    'date' => buatTanggal($d->date),
                    'last_data_date' => $last_data_date->date,
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ];
            }
        }

        // dd('sini');
        $chunks = array_chunk($dataArr, 100);
        foreach ($chunks as $chunk) {
            Jamkerjaid::insert($chunk);
        }
    }

    // dd('first step done');
    // echo 'rekap done';

    // ok2 perhitungan payroll

    $datas = Jamkerjaid::with('karyawan', 'yfrekappresensi')
        ->whereBetween('date', [Carbon::parse($year . '-' . $month . '-01'), Carbon::parse($year . '-' . $month . '-01')->endOfMonth()])
        ->get();

    if ($datas->isEmpty()) {
        return 0;
    }

    $subtotal = 0;
    $denda_noscan = 0;


    Payroll::whereMonth('date', $month)
        ->whereYear('date', $year)
        ->delete();
    foreach ($datas as $data) {


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

        $pajak = 0;

        // denda no scan

        if ($data->total_noscan > 3 && trim($data->karyawan->metode_penggajian) == 'Perjam') {
            $denda_noscan = ($data->total_noscan - 3) * ($data->karyawan->gaji_pokok / 198);
        } else {
            $denda_noscan = 0;
        }



        // denda lupa absen

        if ($data->total_noscan == null) {
            $denda_lupa_absen = 0;
        } else {
            if ($data->total_noscan <= 3) {
                $denda_lupa_absen = 0;
            } else {
                $denda_lupa_absen = ($data->total_noscan - 3) * ($data->karyawan->gaji_pokok / 198);
            }
        }
        if ($data->karyawan->etnis == 'China') {
            $denda_lupa_absen = 0; // TKA tidak ada denda absen

        }


        // hapus ini jika sdh kelar
        // $denda_lupa_absen = 0;
        // $denda_noscan = 0;


        $total_bonus_dari_karyawan = 0;
        $total_potongan_dari_karyawan = 0;
        $gaji_libur = 0;
        // lbr
        if ($data->karyawan->etnis !=  'China') {
            $gaji_libur = ($data->jam_kerja_libur * ($data->karyawan->gaji_pokok / 198));
        }
        $total_bonus_dari_karyawan = $data->karyawan->bonus + $data->karyawan->tunjangan_jabatan + $data->karyawan->tunjangan_bahasa + $data->karyawan->tunjangan_skill + $data->karyawan->tunjangan_lembur_sabtu + $data->karyawan->tunjangan_lama_kerja;
        $total_potongan_dari_karyawan = $data->karyawan->iuran_air + $data->karyawan->iuran_locker;
        $pajak = 0;
        $manfaat_libur = 0;
        // $beginning_date = new DateTime("$year-$month-01");
        $beginning_date = buat_tanggal($month, $year);
        // hehehe
        // if ($data->karyawan->tanggal_bergabung >= $beginning_date  || $data->karyawan->status_karyawan == 'Resigned') {
        if ($data->karyawan->status_karyawan == 'Resigned') {
            $manfaat_libur = manfaat_libur_resigned($month, $year, $libur, $data->user_id, $data->karyawan->tanggal_resigned);
        } else {
            $manfaat_libur = manfaat_libur($month, $year, $libur, $data->user_id, $data->karyawan->tanggal_bergabung);
            // $manfaat_libur = $libur->count();
            // if ($data->user_id == 8217) dd($month, $year, $manfaat_libur, $data->karyawan->status_karyawan);
            if ($manfaat_libur > $libur->count()) $manfaat_libur = $libur->count();
            $cx++;
        }
        $gaji_karyawan_bulanan = ($data->karyawan->gaji_pokok / $total_n_hari_kerja) * ($data->total_hari_kerja + $manfaat_libur);




        if (trim($data->karyawan->metode_penggajian) == 'Perjam') {
            $subtotal = $data->jumlah_jam_kerja * ($data->karyawan->gaji_pokok / 198) + $data->jumlah_menit_lembur * $data->karyawan->gaji_overtime;
        } else {
            $subtotal = $gaji_karyawan_bulanan + $data->jumlah_menit_lembur * $data->karyawan->gaji_overtime;
        }



        $tambahan_shift_malam = $data->tambahan_jam_shift_malam * $data->karyawan->gaji_overtime;
        if ($data->karyawan->jabatan_id == 17) {
            $tambahan_shift_malam = $data->tambahan_jam_shift_malam * $data->karyawan->gaji_shift_malam_satpam;
        }

        $libur_nasional = 0;


        //    Hitung BPJS Adjust

        if ($data->karyawan->metode_penggajian == "Perjam") {
            $gaji_bulan_ini = total_gaji_perjam($data->karyawan->gaji_pokok, $data->jumlah_jam_kerja);
        } else {
            // sore
            $gaji_bulan_ini = total_gaji_bulanan(
                $data->karyawan->gaji_pokok,
                $data->total_hari_kerja,
                $total_n_hari_kerja,
                $jumlah_libur_nasional,
                $data->date,
                $data->karyawan->id_karyawan,
                $data->karyawan->status_karyawan

            );
        }
        // if ($data->karyawan->id_karyawan == 8227) dd($gaji_bulan_ini);


        if ($data->karyawan->gaji_pokok != 0) {
            // $gaji_bpjs_adjust = $data->karyawan->gaji_bpjs * $total_gaji_sebelum_tax / $data->karyawan->gaji_pokok;
            $gaji_bpjs_adjust = $data->karyawan->gaji_bpjs * $gaji_bulan_ini / $data->karyawan->gaji_pokok;
        } else {
            // Handle the case where gaji_pokok is zero (e.g., log an error or assign a default value)
            $gaji_bpjs_adjust = 0; // or another fallback value
            error_log("Division by zero error: gaji_pokok is zero for karyawan ID: " . $data->karyawan->id);
        }
        // oioi
        $total_gaji_lembur = $data->jumlah_menit_lembur * $data->karyawan->gaji_overtime;



        //==================
        if ($data->karyawan->gaji_bpjs >= 12000000) {
            $gaji_bpjs_max = 12000000;
        } else {
            $gaji_bpjs_max = $data->karyawan->gaji_bpjs;
        }

        if (
            $data->karyawan->gaji_bpjs >= 10547400
        ) {
            $gaji_jp_max = 10547400;
        } else {
            $gaji_jp_max = $data->karyawan->gaji_bpjs;
        }
        if (
            $data->karyawan->potongan_kesehatan != 0
        ) {
            $kesehatan_company = ($gaji_bpjs_max * 4) / 100;
        } else {
            $kesehatan_company = 0;
        }

        if ($data->karyawan->potongan_JKK) {
            $jkk_company = ($data->karyawan->gaji_bpjs * 0.24) / 100;
            if ($data->karyawan->company_id == 102) {
                $jkk_company = ($data->karyawan->gaji_bpjs * 0.89) / 100;
            }
        } else {
            $jkk_company = 0;
        }

        if ($data->karyawan->potongan_JKM) {
            $jkm_company = ($data->karyawan->gaji_bpjs * 0.3) / 100;
        } else {
            $jkm_company = 0;
        }




        // $total_tax = $data->karyawan->gaji_bpjs +
        $total_tax =
            $gaji_bpjs_adjust +
            $jkk_company +
            $jkm_company +
            $kesehatan_company +
            $total_gaji_lembur +
            $gaji_libur +
            $tambahan_shift_malam;



        $rate_pph21 = get_rate_ter_pph21($data->karyawan->ptkp, $total_tax);
        $pph21 = ($total_tax * $rate_pph21) / 100;




        if ($data->karyawan->metode_penggajian == '') {
            dd('metode penggajian belum diisi', $data->karyawan->id_karyawan);
        }
        $total = $gaji_bulan_ini + $total_gaji_lembur + $gaji_libur + $total_bonus_dari_karyawan + $libur_nasional + $tambahan_shift_malam - $total_potongan_dari_karyawan - $pajak - $jp - $jht - $kesehatan - $tanggungan - $denda_lupa_absen - $pph21;


        Payroll::create([
            'jp' => $jp,
            'jht' => $jht,
            'kesehatan' => $kesehatan,
            'tanggungan' => $tanggungan,
            'jkk' => $jkk,
            'jkm' => $jkm,
            'denda_lupa_absen' => $denda_lupa_absen,
            'gaji_libur' => $gaji_libur,

            'jamkerjaid_id' => $data->id,
            'nama' => $data->karyawan->nama,
            'id_karyawan' => $data->karyawan->id_karyawan,


            'jabatan_id' => $data->karyawan->jabatan_id,
            'company_id' => $data->karyawan->company_id,
            'placement_id' => $data->karyawan->placement_id,
            'department_id' => $data->karyawan->department_id,

            // 'departemen' => $data->karyawan->department->nama_department,
            'status_karyawan' => $data->karyawan->status_karyawan,
            'metode_penggajian' => $data->karyawan->metode_penggajian,
            'nomor_rekening' => $data->karyawan->nomor_rekening,
            'nama_bank' => $data->karyawan->nama_bank,
            'gaji_pokok' => $data->karyawan->gaji_pokok,
            'gaji_lembur' => $data->karyawan->gaji_overtime,
            'gaji_bpjs' => $data->karyawan->gaji_bpjs,
            'ptkp' => $data->karyawan->ptkp,
            // oll
            'libur_nasional' => $libur_nasional,

            // 'jkk' => $data->karyawan->jkk,
            // 'jkm' => $data->karyawan->jkm,
            'hari_kerja' => $data->total_hari_kerja,
            'jam_kerja' => $data->jumlah_jam_kerja,
            'jam_lembur' => $data->jumlah_menit_lembur,
            'jumlah_jam_terlambat' => $data->jumlah_jam_terlambat,
            'total_noscan' => $data->total_noscan,
            'thr' => $data->karyawan->bonus,
            'tunjangan_jabatan' => $data->karyawan->tunjangan_jabatan,
            'tunjangan_bahasa' => $data->karyawan->tunjangan_bahasa,
            'tunjangan_skill' => $data->karyawan->tunjangan_skill,
            'tunjangan_lama_kerja' => $data->karyawan->tunjangan_lama_kerja,
            'tunjangan_lembur_sabtu' => $data->karyawan->tunjangan_lembur_sabtu,
            'iuran_air' => $data->karyawan->iuran_air,
            'iuran_locker' => $data->karyawan->iuran_locker,
            'tambahan_jam_shift_malam' => $data->tambahan_jam_shift_malam,
            'tambahan_shift_malam' => $tambahan_shift_malam,
            'subtotal' => $subtotal,
            'date' => buatTanggal($data->date),
            'pph21' => $pph21,

            'total' => $total,
            'total_bpjs' => $total_tax,
            'bpjs_adjustment' => $gaji_bpjs_adjust,
            'gaji_bulan_ini' => $gaji_bulan_ini
            // 'created_at' => now()->toDateTimeString(),
            // 'updated_at' => now()->toDateTimeString()
        ]);

        // if ($data->karyawan->id_karyawan == 1662) {
        //     dd('t', $total_tax, $total);
        // }
    }

    // $chunks = array_chunk($payrollArr, 100);
    // foreach ($chunks as $chunk) {
    //     Payroll::insert($chunk);
    // }

    // dd('payroll done');
    // ok3
    // Bonus dan Potongan

    $bonus = 0;
    $potongaan = 0;
    $all_bonus = 0;
    $all_potongan = 0;
    $bonuspotongan = Bonuspotongan::whereMonth('tanggal', $month)
        ->whereYear('tanggal', $year)
        ->get();

    foreach ($bonuspotongan as $d) {
        // Pastikan setiap nilai tidak null
        $all_bonus = ($d->uang_makan ?? 0) + ($d->bonus_lain ?? 0);
        $all_potongan =
            ($d->baju_esd ?? 0) + ($d->gelas ?? 0) + ($d->sandal ?? 0) +
            ($d->seragam ?? 0) + ($d->sport_bra ?? 0) + ($d->hijab_instan ?? 0) +
            ($d->id_card_hilang ?? 0) + ($d->masker_hijau ?? 0) + ($d->potongan_lain ?? 0);

        $payroll = Payroll::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('id_karyawan', $d->user_id)
            ->first();

        if ($payroll) {
            $bonus1x_new = $payroll->bonus1x + $all_bonus;
            $potongan1x_new = $payroll->potongan1x + $all_potongan;
            $total_new = $payroll->total + $all_bonus - $all_potongan;
            $total_bpjs_new = $payroll->total_bpjs + $bonus1x_new;

            // Update payroll langsung
            $payroll->update([
                'bonus1x' => $bonus1x_new,
                'potongan1x' => $potongan1x_new,
                'total' => $total_new,
                'total_bpjs' => $total_bpjs_new,
            ]);;
            // }
        }
    }


    // foreach ($bonuspotongan as $d) {
    //     $all_bonus = $d->uang_makan + $d->bonus_lain;
    //     $all_potongan = $d->baju_esd + $d->gelas + $d->sandal + $d->seragam + $d->sport_bra + $d->hijab_instan + $d->id_card_hilang + $d->masker_hijau + $d->potongan_lain;
    //     $id_payroll = Payroll::whereMonth('date', $month)
    //         ->whereYear('date', $year)
    //         ->where('id_karyawan', $d->user_id)
    //         ->first();
    //     if ($id_payroll != null) {
    //         $payroll = Payroll::find($id_payroll->id);
    //         $payroll->bonus1x = $payroll->bonus1x + $all_bonus;
    //         $payroll->potongan1x = $payroll->potongan1x + $all_potongan;
    //         $payroll->total = $payroll->total + $all_bonus - $all_potongan;
    //         $payroll->total_bpjs = $payroll->total_bpjs + $payroll->bonus1x;
    //         $payroll->save();

    //         if ($payroll->id_karyawan == 1662) dd('total tax: ', $payroll->total_bpjs, $payroll->total_bpjs, $payroll->bonus1x);
    //         // if ($payroll->id_karyawan  == 5794) {
    //         //     dd(
    //         //         $payroll->total_bpjs,
    //         //         $payroll->bonus1x
    //         //     );
    //         // }
    //     }
    // }




    //  ok4
    // hitung ulang PPH21 utk karyawan bulanan yang ada bonus tambahan

    $karyawanWithBonus = Payroll::whereMonth('date', $month)
        ->whereYear('date', $year)
        ->where('bonus1x', '>', 0)->get();

    foreach ($karyawanWithBonus as $kb) {

        $total_bpjs_company = 0;
        $total_bpjs_lama = $kb->total_bpjs;
        $total_bpjs_company = $total_bpjs_lama;
        // if ($kb->id_karyawan == 1099) dd($kb->total);

        // kore

        // if ($kb->metode_penggajian == "Perjam") {
        //     $gaji_bulan_ini = total_gaji_perjam($kb->gaji_pokok, $data->jumlah_jam_kerja);
        // } else {

        //     $gaji_bulan_ini = total_gaji_bulanan(
        //         $kb->gaji_pokok,
        //         $kb->hari_kerja,
        //         $total_n_hari_kerja,
        //         $jumlah_libur_nasional,
        //         $kb->date,
        //         $kb->id_karyawan,
        //         $kb->status_karyawan
        //     );
        // }




        if ($kb->gaji_pokok != 0) {
            $gaji_bpjs_adjust = $kb->gaji_bpjs * $kb->gaji_bulan_ini / $kb->gaji_pokok;
        } else {
            // Handle the case where gaji_pokok is zero (e.g., log an error or assign a default value)
            $gaji_bpjs_adjust = 0; // or another fallback value
            error_log("Division by zero error: gaji_pokok is zero for karyawan ID: " . $kb->id);
        }
        // perhitung jkk jkm kesehatan

        $data_karyawan = Karyawan::where('id_karyawan', $kb->id_karyawan)->first();
        if ($data_karyawan->gaji_bpjs >= 12000000) {
            $gaji_bpjs_max = 12000000;
        } else {
            $gaji_bpjs_max = $data_karyawan->gaji_bpjs;
        }

        if (
            $data_karyawan->potongan_kesehatan != 0
        ) {
            $kesehatan_company = ($gaji_bpjs_max * 4) / 100;
        } else {
            $kesehatan_company = 0;
        }
        // if ($data_karyawan->id_karyawan == 106) dd(
        //     $data_karyawan->potongan_kesehatan,
        //     $kesehatan_company,
        //     $gaji_bpjs_max
        // );

        if ($data_karyawan->potongan_JKK) {
            $jkk_company = ($data_karyawan->gaji_bpjs * 0.24) / 100;
        } else {
            $jkk_company = 0;
        }

        if ($data_karyawan->potongan_JKM) {
            $jkm_company = ($data_karyawan->gaji_bpjs * 0.3) / 100;
        } else {
            $jkm_company = 0;
        }

        // perhitung jkk jkm end


        $total_bpjs_company =
            $kb->bpjs_adjustment +
            // $d->gaji_bpjs +
            $jkk_company +
            $jkm_company +
            // $kb->kesehatan_company +
            $kesehatan_company +
            $kb->gaji_lembur * $kb->jam_lembur +
            $kb->gaji_libur +
            $kb->bonus1x +
            $kb->tambahan_shift_malam;


        $rate_pph21 = get_rate_ter_pph21($kb->ptkp, $total_bpjs_company);
        $pph21_bonus = ($total_bpjs_company * $rate_pph21) / 100;



        $pph21_lama = $kb->pph21;
        $total_lama = $kb->total;

        $kb->pph21 = $pph21_bonus;
        $kb->total_bpjs = $total_bpjs_company;
        $kb->total = $total_lama + $pph21_lama - $pph21_bonus;
        // $kb->total = $kb->total_bpjs - $pph21_bonus;
        // if ($kb->id_karyawan == 1099) dd($kb->total, $total_lama, $pph21_lama, $pph21_bonus);
        $kb->save();




        // if ($kb->id_karyawan == 5794) {
        //     dd($kb->total_bpjs);
        // }
    }


    // ok5
    // perhitungan untuk karyawan yg resign sebelum 3 bulan

    $data = Karyawan::where('tanggal_resigned', '!=', null)
        ->whereMonth('tanggal_resigned', $month)
        ->whereYear('tanggal_resigned', $year)
        ->get();

    foreach ($data as $d) {
        $lama_bekerja = lama_bekerja($d->tanggal_bergabung, $d->tanggal_resigned);
        if ($lama_bekerja <= 90) {
            $data_payrolls = Payroll::where('id_karyawan', $d->id_karyawan)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->first();

            if ($data_payrolls != null) {
                $data_payroll = Payroll::find($data_payrolls->id);
            } else {
                $data_payroll = null;
            }

            if ($data_payroll != null) {
                if (trim($data_payroll->metode_penggajian) == 'Perbulan') {
                    $data_payroll->denda_resigned = 3 * ($data_payroll->gaji_pokok / $total_n_hari_kerja);
                } else {
                    $data_payroll->denda_resigned = 24 * ($data_payroll->gaji_pokok / 198);
                }
                $data_payroll->total = $data_payroll->total - $data_payroll->denda_resigned;
                if ($data_payroll->total < 0) {
                    $data_payroll->total = 0;
                }
                $data_payroll->save();
            }
        }
    }

    // ok 6
    //  Zheng Guixin 1
    // Eddy Chan 2
    // Yang Xiwen 3
    // Rudy Chan 4
    // Yin kai 5
    // Li meilian 25
    // Wanto 6435
    // Chan Kai Wan 6


    $idArrTionghoa = [4, 2, 6435]; // TKA hanya 3 orang
    // $idKhusus = [4, 2, 6435, 1, 3, 5, 6, 21, 22, 23, 24,  26,  28, 30, 31, 32, 33, 34, 35, 800,  5576, 6566, 7511, 6576, 6577, 6578, 6579, 8127, 7613, 8227, 8228, 8233, 8234, 8235]; //TKA hanya 3 no didepan
    // update juga gaji khusus yg di helpers line 84

    $idKhusus = Karyawan::where('etnis', 'China')
        ->whereNotIn('status_karyawan', ['Blacklist', 'Resigned'])
        // ->whereNot('status_karyawan', 'Blacklist')
        ->pluck('id_karyawan')
        ->toArray();
    $idKhusus = array_merge($idKhusus, $idArrTionghoa);
    foreach ($idKhusus as $id) {
        // bonus dan potongan

        $all_bonus = 0;
        $all_potongan = 0;
        $bonuspotongan = Bonuspotongan::whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)->where('user_id', $id)
            ->first();
        if ($bonuspotongan) {
            $all_bonus = $bonuspotongan->uang_makan + $bonuspotongan->bonus_lain;
            $all_potongan = $bonuspotongan->baju_esd + $bonuspotongan->gelas + $bonuspotongan->sandal + $bonuspotongan->seragam + $bonuspotongan->sport_bra + $bonuspotongan->hijab_instan + $bonuspotongan->id_card_hilang + $bonuspotongan->masker_hijau + $bonuspotongan->potongan_lain;
        } else {
            $all_bonus = 0;
            $all_potongan = 0;
        }


        // End

        $data_id = Karyawan::where('id_karyawan', $id)->first();
        $data_karyawan = Karyawan::find($data_id->id);

        if ($data_karyawan->potongan_JP == 1) {
            if ($data_karyawan->gaji_bpjs <= 10547400) {
                $jp = $data_karyawan->gaji_bpjs * 0.01;
            } else {
                $jp = 10547400 * 0.01;
            }
        } else {
            $jp = 0;
        }

        if ($data_karyawan->potongan_JHT == 1) {
            $jht = $data_karyawan->gaji_bpjs * 0.02;
        } else {
            $jht = 0;
        }

        if ($data_karyawan->potongan_kesehatan == 1) {
            $data_gaji_bpjs = 0;
            if ($data_karyawan->gaji_bpjs >= 12000000) $data_gaji_bpjs = 12000000;
            else $data_gaji_bpjs = $data_karyawan->gaji_bpjs;
            $kesehatan = $data_gaji_bpjs * 0.01;
            $kesehatan_company = ($data_gaji_bpjs * 4) / 100;
        } else {
            $kesehatan = 0;
            $kesehatan_company = 0;
        }

        if ($data_karyawan->potongan_JKK == 1) {
            $jkk = 1;
        } else {
            $jkk = 0;
        }
        if ($data_karyawan->potongan_JKM == 1) {
            $jkm = 1;
        } else {
            $jkm = 0;
        }




        if ($data_karyawan->potongan_JKK) {
            $jkk_company = ($data_karyawan->gaji_bpjs * 0.24) / 100;
        } else {
            $jkk_company = 0;
        }

        if ($data_karyawan->potongan_JKM) {
            $jkm_company = ($data_karyawan->gaji_bpjs * 0.3) / 100;
        } else {
            $jkm_company = 0;
        }

        if (
            $data_karyawan->gaji_pokok != 0
        ) {
            // $gaji_bpjs_adjust = $data_karyawan->gaji_bpjs * $total_gaji_sebelum_tax / $data_karyawan->gaji_pokok;
            $gaji_bpjs_adjust = $data_karyawan->gaji_bpjs * $data_karyawan->gaji_pokok / $data_karyawan->gaji_pokok;
        } else {
            // Handle the case where gaji_pokok is zero (e.g., log an error or assign a default value)
            $gaji_bpjs_adjust = 0; // or another fallback value
            error_log("Division by zero error: gaji_pokok is zero for karyawan ID: " . $data_karyawan->id);
        }
        // hitung pph21
        $pph21 = hitung_pph21(
            $data_karyawan->gaji_bpjs,
            // $gaji_bpjs_adjust,
            $data_karyawan->ptkp,
            $data_karyawan->potongan_JHT,
            $data_karyawan->potongan_JP,
            $data_karyawan->potongan_JKK,
            $data_karyawan->potongan_JKM,
            $data_karyawan->potongan_kesehatan,
            0,
            0,
            0,
            0,
            $data_karyawan->company_id
        );



        $total_tax = $gaji_bpjs_adjust + $jkk_company + $jkm_company + $kesehatan_company;





        $is_exist = Payroll::where('id_karyawan', $id)->whereMonth('date', $month)
            ->whereYear('date', $year)->first();
        if ($is_exist) {
            $data = Payroll::find($is_exist->id);
            $data->bpjs_adjustment = $gaji_bpjs_adjust;
            if ($data->metode_penggajian == "Perjam") {
                $data->gaji_bulan_ini = total_gaji_perjam($data->gaji_pokok, $data->jam_kerja);
            } else {
                // sore
                $data->gaji_bulan_ini = total_gaji_bulanan(
                    $data->gaji_pokok,
                    $data->hari_kerja,
                    $total_n_hari_kerja,
                    $jumlah_libur_nasional,
                    $data->date,
                    $data->id_karyawan,
                    $data->status_karyawan
                );
            }
            $data->jp = $jp;
            $data->jht = $jht;
            $data->kesehatan = $kesehatan;
            $data->nama = $data_karyawan->nama;
            $data->id_karyawan = $data_karyawan->id_karyawan;
            $data->jabatan_id = $data_karyawan->jabatan_id;
            $data->company_id = $data_karyawan->company_id;
            $data->department_id = $data_karyawan->department_id;
            $data->placement_id = $data_karyawan->placement_id;
            $data->status_karyawan = $data_karyawan->status_karyawan;
            $data->metode_penggajian = $data_karyawan->metode_penggajian;
            $data->nomor_rekening = $data_karyawan->nomor_rekening;
            $data->nama_bank = $data_karyawan->nama_bank;
            $data->gaji_pokok = $data_karyawan->gaji_pokok;
            $data->gaji_bpjs = $data_karyawan->gaji_bpjs;
            $data->ptkp = $data_karyawan->ptkp;
            $data->jkk = $jkk;
            $data->jkm = $jkm;
            $data->date = $year . '-' . $month . '-01';
            $data->pph21  = $pph21;
            $data->subtotal = $data_karyawan->gaji_pokok;
            $data->bonus1x = $all_bonus;
            $data->potongan1x = $all_potongan;
            $data->total = $data_karyawan->gaji_pokok - ($jp + $jht + $kesehatan) - $pph21 - $all_potongan + $all_bonus;
            $data->hari_kerja = $total_n_hari_kerja - $jumlah_libur_nasional;
            $data->jam_kerja = 0;
            $data->jam_lembur = 0;
            $data->total_bpjs = $total_tax;

            $data->save();
        } else {
            $data = new Payroll();
            $data->bpjs_adjustment = $gaji_bpjs_adjust;
            $data->gaji_bulan_ini = $data_karyawan->gaji_pokok;

            $data->jp = $jp;
            $data->jht = $jht;
            $data->kesehatan = $kesehatan;
            $data->nama = $data_karyawan->nama;
            $data->id_karyawan = $data_karyawan->id_karyawan;

            // $data->jabatan = nama_jabatan($data_karyawan->jabatan_id);
            // $data->company = nama_company($data_karyawan->company_id);
            // $data->departemen = nama_department($data_karyawan->department_id);
            // $data->placement = nama_placement($data_karyawan->placement_id);

            $data->jabatan_id = $data_karyawan->jabatan_id;
            $data->company_id = $data_karyawan->company_id;
            $data->department_id = $data_karyawan->department_id;
            $data->placement_id = $data_karyawan->placement_id;

            $data->status_karyawan = $data_karyawan->status_karyawan;
            $data->metode_penggajian = $data_karyawan->metode_penggajian;
            $data->nomor_rekening = $data_karyawan->nomor_rekening;
            $data->nama_bank = $data_karyawan->nama_bank;
            $data->gaji_pokok = $data_karyawan->gaji_pokok;
            $data->gaji_bpjs = $data_karyawan->gaji_bpjs;
            $data->ptkp = $data_karyawan->ptkp;
            $data->jkk = $jkk;
            $data->jkm = $jkm;
            $data->date = $year . '-' . $month . '-01';
            $data->pph21  = $pph21;
            $data->subtotal = $data_karyawan->gaji_pokok;
            $data->bonus1x = $all_bonus;
            $data->potongan1x = $all_potongan;
            $data->total = $data_karyawan->gaji_pokok - ($jp + $jht + $kesehatan) - $pph21 - $all_potongan + $all_bonus;
            $data->hari_kerja = $total_n_hari_kerja - $jumlah_libur_nasional;
            $data->jam_kerja = 0;
            $data->jam_lembur = 0;
            $data->total_bpjs = $total_tax;
            // dd($total_tax, $data_karyawan->id_karyawan);


            $data->save();
        }
    }

    // ok 6
    // Libur nasional dan resigned sebelum 3 bulan bekerja

    $jumlah_libur_nasional = Liburnasional::whereMonth('tanggal_mulai_hari_libur', $month)
        ->whereYear('tanggal_mulai_hari_libur', $year)
        ->sum('jumlah_hari_libur');

    $current_date = Jamkerjaid::orderBy('date', 'desc')->first();

    // prf
    $data = Payroll::whereMonth('date', $month)
        ->whereYear('date', $year)->get();

    foreach ($data as $d) {
        $total_gaji_lembur = $d->gaji_lembur * $d->jam_lembur;
        $prf_salary = $d->tambahan_shift_malam + $total_gaji_lembur + $d->gaji_libur +  $d->bonus1x + $d->bpjs_adjustment;
        $other_deduction = $d->potongan1x + $d->denda_lupa_absen + $d->denda_resigned + $d->tanggungan + $d->iuran_air + $d->iuran_locker;
        $bpjs_employee = $d->jht + $d->jp + $d->kesehatan;
        $prf = $prf_salary - $other_deduction - $bpjs_employee - $d->pph21;
        $core_cash = $d->gaji_bulan_ini - $d->bpjs_adjustment;
        $d->prf_salary = $prf_salary;
        $d->other_deduction = $other_deduction;
        $d->bpjs_employee = $bpjs_employee;
        $d->prf = $prf;
        $d->core_cash = $core_cash;

        $d->save();
    }


    $lock = Lock::find(1);
    $lock->rebuild_done = 1;
    $lock->save();

    return 1;
}
