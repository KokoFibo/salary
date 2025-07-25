<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;
use App\Exports\KaryawanExport;
use App\Exports\KaryawanTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MultipleKaryawanForm;
use Illuminate\Support\Facades\Storage;
use App\Models\Karyawan;

class ExcelController extends Controller
{
    public function template_gaji()
    {
        Storage::disk('local')->makeDirectory('exports');

        $karyawans = \App\Models\Karyawan::all();


        $filtered = $karyawans->whereIn('status_karyawan', ['PKWT', 'PKWTT', 'Dirumahkan']);

        $grouped = $filtered->groupBy(function ($item) {
            return $item->placement_id . '-' . $item->department_id;
        });



        $zipFilename = 'exports/Template Form Salary Adjust by Placement - Department For Non OS.zip';
        $zipPath = storage_path("app/{$zipFilename}");

        $zip = new ZipArchive;

        $openResult = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($openResult !== true) {
            \Log::error('ZipArchive gagal membuka file zip. Error code: ' . $openResult);
            return response()->json(['error' => 'Gagal buat ZIP, kode error: ' . $openResult], 500);
        }

        $storedFiles = [];

        foreach ($grouped as $key => $karyawanGroup) {
            [$placementId, $departmentId] = explode('-', $key);
            $nama_placement = nama_placement($placementId);
            $nama_department = nama_department($departmentId);

            \Log::info("📦 Proses: placement_id = $placementId, department_id = $departmentId, count = " . $karyawanGroup->count());

            $relativePath = "exports/placement_{$nama_placement}_department_{$nama_department}.xlsx";
            $header_text = "Data Karyawan Non OS Placement {$nama_placement} - Department {$nama_department} - " . now()->format('d-m-Y H:i:s');
            $stored = Excel::store(new KaryawanTemplateExport($karyawanGroup, $header_text), $relativePath, 'local');
            $fullPath = storage_path("app/{$relativePath}");

            if ($stored && file_exists($fullPath)) {
                $zip->addFile($fullPath, "placement_{$nama_placement}/placement_{$nama_placement}_department_{$nama_department}_Non_OS.xlsx");
                $storedFiles[] = $relativePath;
                \Log::info("✅ Berhasil simpan Excel di: {$fullPath}");
            } else {
                \Log::error("❌ Gagal simpan Excel: {$fullPath}");
            }
        }




        if (empty($storedFiles)) {
            \Log::error('Tidak ada file Excel yang berhasil dibuat untuk ZIP.');
            $zip->close();
            return response()->json(['error' => 'Tidak ada file Excel untuk dimasukkan ke ZIP'], 400);
        }

        $zip->close();

        if (!file_exists($zipPath)) {
            \Log::error("File ZIP tidak ditemukan setelah close: {$zipPath}");
            return response()->json(['error' => 'File ZIP tidak ditemukan setelah dibuat'], 500);
        }

        Storage::disk('local')->delete($storedFiles);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
    public function template_gaji_placement()
    {
        Storage::disk('local')->makeDirectory('exports');

        $karyawans = \App\Models\Karyawan::all();

        $filtered = $karyawans->whereIn('status_karyawan', ['PKWT', 'PKWTT', 'Dirumahkan']);

        $grouped = $filtered->groupBy('placement_id');

        $zipFilename = 'exports/Template Form Salary Adjust by Directorate only For Non OS.zip';
        $zipPath = storage_path("app/{$zipFilename}");

        $zip = new ZipArchive;

        $openResult = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($openResult !== true) {
            \Log::error('ZipArchive gagal membuka file zip. Error code: ' . $openResult);
            return response()->json(['error' => 'Gagal buat ZIP, kode error: ' . $openResult], 500);
        }

        $storedFiles = [];

        foreach ($grouped as $placementId => $karyawanGroup) {
            $nama_placement = nama_placement($placementId);

            \Log::info("📦 Proses: placement_id = $placementId, count = " . $karyawanGroup->count());

            $relativePath = "exports/Directorate_{$nama_placement}.xlsx";
            $header_text = "Data Karyawan Non OS Directorate {$nama_placement} - " . now()->format('d-m-Y H:i:s');
            $stored = Excel::store(new KaryawanTemplateExport($karyawanGroup, $header_text), $relativePath, 'local');
            $fullPath = storage_path("app/{$relativePath}");

            if ($stored && file_exists($fullPath)) {
                $zip->addFile($fullPath, "Directorate_{$nama_placement}/Directorate_{$nama_placement}_NON_OS.xlsx");
                $storedFiles[] = $relativePath;
                \Log::info("✅ Berhasil simpan Excel di: {$fullPath}");
            } else {
                \Log::error("❌ Gagal simpan Excel: {$fullPath}");
            }
        }

        if (empty($storedFiles)) {
            \Log::error('Tidak ada file Excel yang berhasil dibuat untuk ZIP.');
            $zip->close();
            return response()->json(['error' => 'Tidak ada file Excel untuk dimasukkan ke ZIP'], 400);
        }

        $zip->close();

        if (!file_exists($zipPath)) {
            \Log::error("File ZIP tidak ditemukan setelah close: {$zipPath}");
            return response()->json(['error' => 'File ZIP tidak ditemukan setelah dibuat'], 500);
        }

        Storage::disk('local')->delete($storedFiles);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
