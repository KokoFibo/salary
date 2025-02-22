<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicantdata;
use App\Models\Yfrekappresensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Karyawan;



class ApplicantController extends Controller
{



    public function mergeFilesToPdf($folder)
    {
        $folderPath = "public/Applicants/{$folder}";
        $files = Storage::files($folderPath);
        $data = [];

        // Ambil data karyawan berdasarkan id_file_karyawan
        $data_karyawan = Karyawan::where('id_file_karyawan', $folder)->first();

        // Cek apakah data karyawan ditemukan
        $nama_karyawan = $data_karyawan ? $data_karyawan->nama : 'Nama Tidak Ditemukan';

        foreach ($files as $file) {
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $data[] = [
                'name' => basename($file), // Nama file
                'type' => in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? 'image' : 'text',
                'path' => storage_path("app/$file"),
            ];
        }

        // Generate PDF dengan data yang sudah diproses
        $pdf = Pdf::loadView('pdf.merged-files', compact('folder', 'data', 'nama_karyawan'));

        return $pdf->download("{$folder}.pdf");
    }



    public function download($folder)
    {
        // ganti Applicantdata dengan Karyawan jika sudah diterima sebagai karyawan

        $data = Karyawan::where('id_file_karyawan', $folder)->first();
        $zip_nama = $data->nama;
        $folderPath = "public/Applicants/{$folder}";
        // $zipFileName = "{$folder}.zip";
        $zipFileName = "{$zip_nama}.zip";
        $zipFilePath = storage_path("app/public/$zipFileName");

        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = Storage::allFiles($folderPath);
            foreach ($files as $file) {
                $relativePath = str_replace("$folderPath/", '', $file);
                $zip->addFile(storage_path("app/$file"), $relativePath);
            }
            $zip->close();
        } else {
            return response()->json(['error' => 'Could not create zip file'], 500);
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }


    public function login(Request $request)
    {
        $validated = $request->validate([

            'email' => 'required|email',
            'password' => 'required|min:8'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email harus benar',
            'password.required' => 'Passward wajib diisi',
            'password.min' => 'Password harus diisi minimal 8 karakter'
        ]);

        $infoLogin = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        if (Auth::attempt($infoLogin)) {
            dd('sukses');
        } else {
            dd('gagal');
        }
    }
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ], [
            'nama.required' => 'Nama wajib diisi',
            'nama.min' => 'Nama harus diisi minimal 3 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email harus benar',
            'password.required' => 'Passward wajib diisi',
            'password.min' => 'Password harus diisi minimal 8 karakter'
        ]);

        Applicantdata::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return view('applicant.login')->with('success', 'Berhasil di register');
    }
}
