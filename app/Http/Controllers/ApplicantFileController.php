<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ApplicantFile;
use Illuminate\Http\JsonResponse;

class ApplicantFileController extends Controller
{
    public function index($id_karyawan): JsonResponse
    {
        $files = ApplicantFile::where('id_karyawan', $id_karyawan)
            ->orderBy('id', 'desc')
            ->get();

        if ($files->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
                'data' => []
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil.',
            'data' => $files
        ]);
    }
}
