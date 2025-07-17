<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Ter;
use App\Models\User;
use App\Models\Company;
use App\Models\Jabatan;
use App\Models\Payroll;
use Livewire\Component;
use App\Models\Karyawan;
use App\Models\Tambahan;
use App\Models\Placement;
use App\Models\Requester;
use App\Models\Department;
use App\Models\Jamkerjaid;
use App\Models\Rekapbackup;
use Livewire\WithPagination;
use App\Models\Bonuspotongan;
use App\Models\Liburnasional;
use App\Models\Yfrekappresensi;
use Illuminate\Support\Facades\DB;
use App\Models\Personnelrequestform;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Google\Service\YouTube\ThirdPartyLinkStatus;

class Test extends Component
{
  // public $saturday;
  use WithPagination;
  protected $paginationTheme = 'bootstrap';
  public $month;
  public $year;
  public $today;
  public $cx;



  public function mount()
  {
    $this->cx = 0;
    $this->today = now();

    $this->year = now()->year;
    $this->month = now()->month;
  }

  public function render()
  {
    $karyawans = Karyawan::where('level_jabatan', '!=', "")->get();
    foreach ($karyawans as $karyawan) {
      $karyawan->level_jabatan = "";
      $karyawan->save();
    }
    dd($karyawans);

    $data = Yfrekappresensi::where('date', '2025-05-30')
      // ->whereMonth('date', 5)
      //   ->whereYear('date', 2025)
      ->where(function ($query) {
        $query->where(function ($q) {
          $q->whereNull('first_in')
            ->whereNull('first_out');
        })->orWhere(function ($q) {
          $q->whereNull('second_in')
            ->whereNull('second_out');
        });
      })
      ->get();
    return view('livewire.test', [
      'data' => $data
    ]);
  }
}
