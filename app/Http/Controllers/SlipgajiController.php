<?php

namespace App\Http\Controllers;

use App\Models\Yfrekappresensi;
use Illuminate\Http\Request;

class SlipgajiController extends Controller
{
    public function getData($id)
    {
        $data = Yfrekappresensi::where('user_id', $id)->whereMonth('date', 12)->whereYear('date', 2024)->get();
        return $data;
    }
}
