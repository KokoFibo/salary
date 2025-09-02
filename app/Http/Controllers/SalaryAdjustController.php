<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Models\Bonuspotongan;
use App\Models\Jobgrade;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SalaryAdjustController extends Controller
{

    function extractTanggal($string)
    {
        // Cari pola tanggal dengan format dd-mm-yyyy
        // preg_match('/\d{2}-\d{2}-\d{4}/', $string, $match);
        preg_match('/\d{1,2}-\d{1,2}-\d{4}/', $string, $match);

        if (!empty($match)) {
            $tanggal = $match[0];

            // Buat objek DateTime dari format d-m-Y
            $date = DateTime::createFromFormat('d-m-Y', $tanggal);

            // Pastikan parsing berhasil
            if ($date) {
                return $date->format('Y-m-d');
            }
        }

        // Jika tidak ditemukan atau gagal parsing
        return null;
    }


    public function import1(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Validasi header baris ke-4 (index 3)
        $expectedHeader = ['ID Employee', 'Nama', 'Departemen', 'Posisi Jabatan', 'Waktu Gabung', 'Bulan Penyesuaian Sebelumnya', 'Alasan', 'Gaji Sebelum', 'Jumlah Penyesuaian', 'Gaji Sesudah', 'Lemburan Awal', 'Perubahan Lemburan', 'Bonus'];
        $actualHeader = $rows[4]; // baris header

        if (array_diff($expectedHeader, $actualHeader)) {
            return back()->withErrors(['file' => 'Header file tidak sesuai format yang diharapkan.']);
        }

        $jumlahUpdate = 0;

        foreach ($rows as $index => $row) {
            if ($index < 5) continue; // skip header dan info awal
            $id_karyawan = isset($row[0]) ? (int) str_replace(',', '', $row[0]) : null;
            $nama = $row[1];
            $gaji_raw = $row[9] ?? null;
            $lembur_raw = $row[11] ?? null;

            $gaji_sesudah = null;
            $lembur_baru = null;

            if ($gaji_raw !== null && $gaji_raw !== '') {
                if (preg_match('/^[\d,\.]+$/', $gaji_raw)) {
                    $gaji_sesudah = (int) str_replace([',', '.'], '', $gaji_raw);
                } else {
                    return back()->with('error', "{$row[1]} - ID: {$row[0]}, GAJI POKOK di file excel harus numeric. {$jumlahUpdate} data karyawan berhasil diperbarui.");
                }
            }

            if ($lembur_raw !== null && $lembur_raw !== '') {
                if (preg_match('/^[\d,\.]+$/', $lembur_raw)) {
                    $lembur_baru = (int) str_replace([',', '.'], '', $lembur_raw);
                } else {
                    return back()->with('error', "{$nama} - ID: {$id_karyawan}, GAJI LEMBUR di file excel harus numeric, {$jumlahUpdate} data karyawan berhasil diperbarui.");
                }
            }

            if ($lembur_raw !== null && $lembur_raw !== '') {
                if (preg_match('/^[\d,\.]+$/', $lembur_raw)) {
                    $lembur_baru = (int) str_replace([',', '.'], '', $lembur_raw);
                } else {
                    return back()->with('error', "{$nama} - ID: {$id_karyawan}, GAJI LEMBUR di file excel harus numeric, {$jumlahUpdate} data karyawan berhasil diperbarui.");
                }
            }
            // if ($id_karyawan == 2792) dd($gaji_sesudah, $lembur_baru);
            if (!$id_karyawan  || !$gaji_sesudah) continue;

            $karyawan = Karyawan::where('id_karyawan', $id_karyawan)->where('nama', $nama)->first();
            // dd($karyawan, $id_karyawan, $nama);
            if ($karyawan) {
                $updated = false;

                if ($karyawan->gaji_tetap != $gaji_sesudah) {
                    $karyawan->gaji_tetap = $gaji_sesudah;
                    $karyawan->gaji_pokok = $karyawan->gaji_tetap + $karyawan->tunjangan_bahasa + $karyawan->tunjangan_housing + $karyawan->tunjangan_jabatan;

                    $updated = true;
                    // dd($karyawan->gaji_tetap, $gaji_sesudah, $updated);
                }

                if ($lembur_baru && $karyawan->gaji_overtime != $lembur_baru) {
                    $karyawan->gaji_overtime = $lembur_baru;
                    $updated = true;
                }

                if ($updated) {
                    $karyawan->tanggal_update = Carbon::now();
                    $karyawan->save();
                    $jumlahUpdate++;
                }
            }
        }

        return back()->with('success', "{$jumlahUpdate} data karyawan berhasil diperbarui.");
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Validasi header baris ke-4 (index 3)
        $expectedHeader = [
            'ID Employee',
            'Nama',
            'Departemen',
            'Posisi Jabatan',
            'Job Grade',
            'Waktu Gabung',
            'Bulan Penyesuaian Sebelumnya',
            'Alasan',
            'Gaji Sebelum',
            'Jumlah Penyesuaian',
            'Gaji Sesudah',
            'Lemburan Awal',
            'Perubahan Lemburan',
            'Bonus',
            'T. Bahasa Sebelum',
            'T. Bahasa Sesudah',
            'T. Jabatan Sebelum',
            'T. Jabatan Sesudah',
            'T. Housing Sebelum',
            'T. Housing Sesudah'
        ];

        $tanggal = Carbon::today()->toDateString();
        // $jobgrades = Jobgrade::pluck('grade')->toArray();
        $jobgrades = Jobgrade::pluck('grade', 'grade')->toArray();

        $jumlahUpdate = 0;

        foreach ($rows as $index => $row) {
            if ($index < 5) continue; // Skip header dan informasi awal

            $id_karyawan = isset($row[0]) ? (int) str_replace(',', '', $row[0]) : null;
            $nama = $row[1];
            $jobGrade_raw = trim($row[4]) ?? null;
            $gaji_raw = $row[10] ?? null;
            $lembur_raw = $row[12] ?? null;
            $bonus_raw = $row[13] ?? null;
            $bahasa_raw = $row[15] ?? null;
            $jabatan_raw = $row[17] ?? null;
            $housing_raw = $row[19] ?? null;

            $jobGrade_sesudah = null;
            $gaji_sesudah = null;
            $lembur_baru = null;
            $bonus_baru = null;
            $bahasa_baru = null;
            $jabatan_baru = null;
            $housing_baru = null;

            // Validasi GAJI SESUDAH
            if ($gaji_raw !== null && $gaji_raw !== '') {
                if (preg_match('/^[\d,\.]+$/', $gaji_raw)) {
                    $gaji_sesudah = (int) str_replace([',', '.'], '', $gaji_raw);
                } else {
                    return back()->with('error', "{$nama} - ID: {$id_karyawan}, GAJI POKOK di file excel harus numeric. {$jumlahUpdate} data karyawan berhasil diperbarui.");
                }
            }

            // Validasi LEMBUR BARU
            if ($lembur_raw !== null && $lembur_raw !== '') {
                if (preg_match('/^[\d,\.]+$/', $lembur_raw)) {
                    $lembur_baru = (int) str_replace([',', '.'], '', $lembur_raw);
                } else {
                    return back()->with('error', "{$nama} - ID: {$id_karyawan}, GAJI LEMBUR di file excel harus numeric. {$jumlahUpdate} data karyawan berhasil diperbarui.");
                }
            }

            // Validasi BONUS BARU
            if ($bonus_raw !== null && $bonus_raw !== '') {
                if (preg_match('/^[\d,\.]+$/', $bonus_raw)) {
                    $bonus_baru = (int) str_replace([',', '.'], '', $bonus_raw);
                } else {
                    return back()->with('error', "{$nama} - ID: {$id_karyawan}, GAJI BONUS di file excel harus numeric. {$jumlahUpdate} data karyawan berhasil diperbarui.");
                }
            }

            // Validasi Tunjangan Bahasa
            if ($bahasa_raw !== null && $bahasa_raw !== '') {
                if (preg_match('/^[\d,\.]+$/', $bahasa_raw)) {
                    $bahasa_baru = (int) str_replace([',', '.'], '', $bahasa_raw);
                } else {
                    return back()->with('error', "{$nama} - ID: {$id_karyawan}, Tunjangan BAHASA di file excel harus numeric. {$jumlahUpdate} data karyawan berhasil diperbarui.");
                }
            }

            // Validasi Tunjangan Jabatan
            if ($jabatan_raw !== null && $jabatan_raw !== '') {
                if (preg_match('/^[\d,\.]+$/', $jabatan_raw)) {
                    $jabatan_baru = (int) str_replace([',', '.'], '', $jabatan_raw);
                } else {
                    return back()->with('error', "{$nama} - ID: {$id_karyawan}, Tunjangan JABATAN di file excel harus numeric. {$jumlahUpdate} data karyawan berhasil diperbarui.");
                }
            }

            // Validasi Tunjangan Housing
            if ($housing_raw !== null && $housing_raw !== '') {
                if (preg_match('/^[\d,\.]+$/', $housing_raw)) {
                    $housing_baru = (int) str_replace([',', '.'], '', $housing_raw);
                } else {
                    return back()->with('error', "{$nama} - ID: {$id_karyawan}, Tunjangan HOUSING di file excel harus numeric. {$jumlahUpdate} data karyawan berhasil diperbarui.");
                }
            }





            // Skip jika ID kosong atau tidak ada data gaji/lembur
            if (!$id_karyawan || ($gaji_sesudah === null && $lembur_baru === null && $bonus_baru === null)) {
                continue;
            }

            $karyawan = Karyawan::where('id_karyawan', $id_karyawan)
                ->where('nama', $nama)
                ->first();


            if ($karyawan) {
                $updated = false;

                // Update gaji_tetap jika berbeda atau meskipun 0
                if ($gaji_sesudah !== null && $karyawan->gaji_tetap != $gaji_sesudah) {
                    $karyawan->gaji_tetap = $gaji_sesudah;

                    $min_BPJS = 0;
                    // dd($karyawan->gaji_bpjs, $karyawan->potongan_kesehatan);
                    if ($karyawan->potongan_kesehatan) {
                        $min_BPJS = 4901117;
                    } else  $min_BPJS = 3850000;
                    if ($gaji_sesudah <= $min_BPJS) $karyawan->gaji_bpjs = $min_BPJS;
                    else $karyawan->gaji_bpjs = $gaji_sesudah;
                    $updated = true;
                }

                // Update gaji_overtime jika berbeda atau meskipun 0
                if ($lembur_baru !== null && $karyawan->gaji_overtime != $lembur_baru) {
                    $karyawan->gaji_overtime = $lembur_baru;
                    $updated = true;
                }

                // Update Tunjangan Bahasa jika berbeda atau meskipun 0
                if ($bahasa_baru !== null && $karyawan->gaji_overtime != $bahasa_baru) {
                    $karyawan->tunjangan_bahasa = $bahasa_baru;
                    $updated = true;
                }

                // Update Tunjangan Jabatan jika berbeda atau meskipun 0
                if ($jabatan_baru !== null && $karyawan->gaji_overtime != $jabatan_baru) {
                    $karyawan->tunjangan_jabatan = $jabatan_baru;
                    $updated = true;
                }

                // Update Tunjangan Housing jika berbeda atau meskipun 0
                if ($housing_baru !== null && $karyawan->gaji_overtime != $housing_baru) {
                    $karyawan->tunjangan_housing = $housing_baru;
                    $updated = true;
                }

                // update Job Grade
                if (!empty($jobGrade_raw) && isset($jobgrades[$jobGrade_raw])) {
                    $jobGrade_sesudah = $jobgrades[$jobGrade_raw];
                    $karyawan->level_jabatan = $jobGrade_sesudah;

                    $updated = true;
                } else {
                    // Kalau tidak ditemukan, kasih default value
                    $jobGrade_sesudah  = null;
                    $karyawan->level_jabatan = $jobGrade_sesudah;
                    $updated = true;
                }


                if ($updated) {
                    // $karyawan->tanggal_update = Carbon::now();
                    $karyawan->gaji_pokok = $karyawan->gaji_tetap + $karyawan->tunjangan_bahasa + $karyawan->tunjangan_housing + $karyawan->tunjangan_jabatan;
                    $karyawan->tanggal_update = Carbon::parse($tanggal);
                    $karyawan->save();
                    $jumlahUpdate++;
                }
                // if ($bonus_baru !== null) {
                if ($bonus_baru > 0) {

                    $data = Bonuspotongan::where('user_id', $id_karyawan)
                        ->whereMonth('tanggal', Carbon::parse($tanggal)->format('m'))
                        ->whereYear('tanggal', Carbon::parse($tanggal)->format('Y'))
                        ->first();
                    // if ($id_karyawan == 2172) dd($data);
                    if (!$data) {
                        // Simpan baru jika belum ada
                        $data = new Bonuspotongan;
                        $data->karyawan_id = $karyawan->id;
                        $data->user_id = $id_karyawan;
                        $data->tanggal = Carbon::parse($tanggal)->format('Y-m-d');
                        $data->bonus_lain = $bonus_baru;
                        $data->save();
                        $jumlahUpdate++;
                    } else {
                        // Update jika sudah ada
                        if ($data->bonus_lain != $bonus_baru) {
                            $data->bonus_lain = $bonus_baru;
                            $data->save();
                            $jumlahUpdate++;
                        }
                    }
                }
            }
        }

        return back()->with('success', "{$jumlahUpdate} data karyawan berhasil diperbarui.");
    }
}
