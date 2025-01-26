<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Models\Yfrekappresensi;

class ApiController extends Controller
{

    public function getJamKerja($user_id, $month, $year)
    {
        $total_hari_kerja = 0;
        $total_jam_kerja = 0;
        $total_jam_lembur = 0;
        $total_keterlambatan = 0;
        $langsungLembur = 0;

        $dataArr = [];
        $data = Yfrekappresensi::where('user_id', $user_id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();

        foreach ($data as $d) {
            if ($d->no_scan == null) {
                $tgl = tgl_doang($d->date);
                $jam_kerja = hitung_jam_kerja($d->first_in, $d->first_out, $d->second_in, $d->second_out, $d->late, $d->shift, $d->date, $d->karyawan->jabatan_id, get_placement($d->user_id));
                $terlambat = late_check_jam_kerja_only($d->first_in, $d->first_out, $d->second_in, $d->second_out, $d->shift, $d->date, $d->karyawan->jabatan_id, get_placement($d->user_id));
                // if($d->shift == 'Malam' || is_jabatan_khusus($d->user_id)) {
                $langsungLembur = langsungLembur($d->second_out, $d->date, $d->shift, $d->karyawan->jabatan_id, get_placement($d->user_id));
                // }
                $jam_lembur = hitungLembur($d->overtime_in, $d->overtime_out) / 60 + $langsungLembur;
                $total_jam_kerja = $total_jam_kerja + $jam_kerja;
                $total_jam_lembur = $total_jam_lembur + $jam_lembur;
                $total_keterlambatan = $total_keterlambatan + $terlambat;

                $dataArr[] = [
                    'tgl' => $tgl,
                    'jam_kerja' => $jam_kerja,
                    'terlambat' => $terlambat,
                    'jam_lembur' => $jam_lembur,
                ];
                $total_hari_kerja++;
            }
        }

        return $dataArr;
    }

    public function getDetailJamKerjaKaryawan($user_id, $month, $year)
    {
        $total_hari_kerja = 0;
        $total_jam_kerja = 0;
        $total_jam_lembur = 0;
        $total_keterlambatan = 0;
        $langsungLembur = 0;

        $dataArr = [];
        $data = Yfrekappresensi::where('user_id', $user_id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();

        foreach ($data as $d) {
            if ($d->no_scan == null) {
                $tgl = tgl_doang($d->date);
                $jam_kerja = hitung_jam_kerja($d->first_in, $d->first_out, $d->second_in, $d->second_out, $d->late, $d->shift, $d->date, $d->karyawan->jabatan_id, get_placement($d->user_id));
                $terlambat = late_check_jam_kerja_only($d->first_in, $d->first_out, $d->second_in, $d->second_out, $d->shift, $d->date, $d->karyawan->jabatan_id, get_placement($d->user_id));
                // if($d->shift == 'Malam' || is_jabatan_khusus($d->user_id)) {
                $langsungLembur = langsungLembur($d->second_out, $d->date, $d->shift, $d->karyawan->jabatan_id, get_placement($d->user_id));
                // }
                $jam_lembur = hitungLembur($d->overtime_in, $d->overtime_out) / 60 + $langsungLembur;
                $total_jam_kerja = $total_jam_kerja + $jam_kerja;
                $total_jam_lembur = $total_jam_lembur + $jam_lembur;
                $total_keterlambatan = $total_keterlambatan + $terlambat;

                $dataArr[] = [
                    'tgl' => $tgl,
                    'jam_kerja' => $jam_kerja,
                    'terlambat' => $terlambat,
                    'jam_lembur' => $jam_lembur,
                ];
                $total_hari_kerja++;
            }
        }
        // Prepare the response data

        $response = [
            'total_hari_kerja' => $total_hari_kerja,
            'total_jam_kerja' => $total_jam_kerja,
            'total_jam_lembur' => $total_jam_lembur,
            'total_keterlambatan' => $total_keterlambatan,
            'detail' => $dataArr,
        ];

        // Return JSON response
        return response()->json($response);
        // return $dataArr;
    }

    public function store($id)
    {

        $respKaryawan = Http::get('https://payroll.yifang.co.id/api/getkaryawan/' . $id);
        $dataKaryawan = $respKaryawan->json();

        $respUser = Http::get('https://payroll.yifang.co.id/api/getuser/' . $id);
        $dataUser = $respUser->json();

        if ($respKaryawan->successful() && $respUser->successful()) {

            // dd('berhasil');
            $karyawan = Karyawan::create($dataKaryawan);
            $user = User::create($dataUser);
            return response()->json(
                [
                    'message' => 'Karyawan created successfully!',
                    'karyawan' => $karyawan
                ],
                200
            );
        } else {
            return response()->json(['error' => 'Data karyawan ini tidak dalam database'], 500);
        }
    }

    public function index()
    {
        return Karyawan::where('id_karyawan', '2')->get();
    }

    // Dibawah ini hanya contoh saja
    // public function getDataUser($id)
    // {
    //     // Find the user by ID
    //     $user = User::where('username', $id)->first();

    //     // Check if the user exists
    //     if (!$user) {
    //         return response()->json([
    //             'message' => 'User not found'
    //         ], 404);
    //     }

    //     // Return user data
    //     return response()->json($user, 200);
    // }
    public function delete_data_user_yf_aja($id)
    {
        try {
            // Find the karyawan by id
            $user = User::where('username', $id)->first();

            // Check if the karyawan exists
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Karyawan not found',
                ], 404);
            }

            // Delete the karyawan record
            $user->delete();

            // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting User',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function delete_data_karyawan_yf_aja($id)
    {
        try {
            // Find the karyawan by id
            $karyawan = Karyawan::where('id_karyawan', $id)->first();

            // Check if the karyawan exists
            if (!$karyawan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Karyawan not found',
                ], 404);
            }

            // Delete the karyawan record
            $karyawan->delete();

            // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'Karyawan deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting karyawan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function getDataUser($id)
    {
        // Find the user by ID
        $user = User::where('username', $id)->first();

        // Check if the user exists
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // Return user data
        return response()->json($user, 200);
    }
    public function getDataKaryawan($id)
    {
        // Find the user by ID
        $karyawan = Karyawan::where('id_karyawan', $id)->first();

        // Check if the user exists
        if (!$karyawan) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // Return user data
        return response()->json($karyawan, 200);
    }

    public function move_data($id)
    {
        // Find the user by ID
        $user = User::where('username', $id)->first();
        $karyawan = Karyawan::where('id_karyawan', $id)->first();

        // Check if the user exists
        if (!$user || !$karyawan) {
            return response()->json([
                'message' => 'User or Karyawan not found'
            ], 404);
        }

        // Return user data
        return response()->json($user, 200);
    }
}
