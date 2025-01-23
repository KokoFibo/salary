<?php

namespace App\Http\Controllers;

use App\Models\Yfrekappresensi;
use Illuminate\Http\Request;

class SlipgajiController extends Controller
{
    public function getData($id, $month, $year)
    {
        $data = Yfrekappresensi::where('user_id', $id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();

        return $data->isEmpty() ? 0 : $data;  // Return 0 if no data is found
    }
}
