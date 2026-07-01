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

  public function generate()
  {


    $tgl = '2026-06-27';

    $karyawanTidakHadir = Karyawan::where('placement_id', 8)
      ->whereIn('status_karyawan', ['PKWT', 'PKWTT'])
      ->where(function ($q) use ($tgl) {
        $q->whereNull('tanggal_resigned')
          ->orWhereDate('tanggal_resigned', '>=', $tgl);
      })
      ->where(function ($q) use ($tgl) {
        $q->whereNull('tanggal_blacklist')
          ->orWhereDate('tanggal_blacklist', '>=', $tgl);
      })
      ->whereNotExists(function ($q) use ($tgl) {
        $q->selectRaw(1)
          ->from('yfrekappresensis')
          ->whereColumn('yfrekappresensis.user_id', 'karyawans.id_karyawan')
          ->whereDate('yfrekappresensis.date', $tgl);
      })
      ->get();

    foreach ($karyawanTidakHadir as $kh) {

      $placement_id = $kh->placement_id;

      $first_in = '08:00';
      $first_out = null;
      $second_in = null;
      $second_out = '15:00';
      $overtime_in = null;
      $overtime_out = null;
      $late = null;
      $no_scan = null;
      $shift = '';


      $gagal_scan = 0;


      Yfrekappresensi::create([
        'shift_malam' => $tambahan_shift_malam ?? 0,
        'user_id' => $kh->id_karyawan,
        'karyawan_id' => $kh->id,
        // 'name' => $name,
        'date' => $tgl,
        'first_in' => $first_in,
        'first_out' => $first_out,
        'second_in' => $second_in,
        'second_out' => $second_out,
        'overtime_in' => $overtime_in,
        'overtime_out' => $overtime_out,
        'total_jam_kerja' => 6,
        'total_hari_kerja' => 1,
        'total_jam_lembur' => null,
        'total_jam_kerja_libur' => null,

        'total_hari_kerja_libur' => null,
        'total_jam_lembur_libur' => null,

        'shift' => 'Pagi',
        'late' => null,
        'no_scan' => null,
        'no_scan_history' => null,
        'late_history' => null,
      ]);
    }
  }

  public function render()
  {
    // $this->generate();
    dd('aman');


    return view('livewire.test');
  }
}
