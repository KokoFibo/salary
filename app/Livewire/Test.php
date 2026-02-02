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

  public function deleteSTI1()
  {
    // company sti = 102
    // placement sti = 104
    $data = Karyawan::where('company_id', 102)->get();
    foreach ($data as $d) {
      $user = User::where('username', $d->id_karyawan)->delete();
      $presensis = Yfrekappresensi::where('user_id', $d->id_karyawan)
        ->whereMonth('date', 9)
        ->whereYear('date', 2025)
        ->delete();
    }
    $data = Karyawan::where('company_id', 102)->delete();
  }
  public function deleteSTI()
  {
    // company sti = 102
    // placement sti = 104
    DB::transaction(function () {
      // Ambil semua karyawan dari company_id = 102
      $karyawans = Karyawan::where('company_id', 102)->get();

      foreach ($karyawans as $karyawan) {
        // Hapus user
        User::where('username', $karyawan->id_karyawan)->delete();

        // Hapus presensi bulan 9 / 2025
        Yfrekappresensi::where('user_id', $karyawan->id_karyawan)
          ->whereMonth('date', 9)
          ->whereYear('date', 2025)
          ->delete();
      }

      // Hapus karyawan terakhir
      Karyawan::where('company_id', 102)->delete();
      $this->dispatch(
        'message',
        type: 'success',
        title: 'Semua data STI telah didelete'
      );
    });
  }

  public function render()
  {
    $year = 2025;
    $month = 12;
    // dd('aman');


    $karyawans = Karyawan::where('potongan_kesehatan', 1)
      ->where('gaji_bpjs', '<', 5210377)
      ->get();

    foreach ($karyawans as $karyawan) {
      $karyawan->gaji_bpjs = 5210377;
      $karyawan->save();
    }

    return view('livewire.test', [
      'karyawans' => $karyawans
    ]);
  }
}
