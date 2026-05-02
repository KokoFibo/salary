<?php

use App\Models\Harikhusus;
use Carbon\Carbon;

function khusus_is_sunday($tgl)
{
    $tgl_khusus = Harikhusus::where('date', $tgl)->first();
    if ($tgl_khusus->is_sunday === 0) {
        return false;
    }
    if ($tgl) {
        return Carbon::parse($tgl)->isSunday();
    }
}

function khusus_is_saturday($tgl)
{
    $tgl_khusus = Harikhusus::where('date', $tgl)->first();
    if ($tgl_khusus->is_saturday === 1) {
        return true;
    } else {
        return false;
    }
}
