<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function storeKaryawan(Request $request)
    {
        dd($request);
        // $id = 8195;
        // $karyawan = Karyawan::where('id_karyawan', $id)->first();
        if (!$request) {
            return response()->json([
                'message' => 'User or Karyawan not found'
            ], 404);
        } else {
            $newKaryawan = $request->replicate();
            $newKaryawan->save();
            return response()->json('SUKSES BRO', 200);
        }
    }
    public function index()
    {
        return Karyawan::where('id_karyawan', '2')->get();
    }
}
