<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function index()
    {
        return Karyawan::where('id_karyawan', '2')->get();
    }
}
