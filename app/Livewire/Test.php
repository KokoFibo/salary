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

  public $department_id, $placement_id;
  public $passBaru;
  // public $karyawan;

  public function proses()
  {
    $this->dispatch(
      'message',
      type: 'success',
      title: 'Gak ngapa ngapain',
    );
  }
  public function getJabatan_id($jabatan)
  {
    if ($jabatan) {
      $data = Jabatan::where('nama_jabatan', $jabatan)->first();
      // dd($data->id);
      return $data->id;
    }
    return 0;
  }
  public function getCompany_id($company)
  {
    if ($company) {
      $data = Company::where('company_name', $company)->first();
      return $data->id;
    }
    return 0;
  }
  public function getPlacement_id($placement)
  {
    if ($placement) {
      if ($placement == 'YCME') return 102;
      if ($placement == 'YEV SMOOT') return 10;
      if ($placement == 'YEV OFFERO') return 9;
      if ($placement == 'YEV ELEKTRONIK') return 101;
      $data = Placement::where('placement_name', $placement)->first();
      return $data->id;
    }
    return 0;
  }

  public function getDepartment_id($department)
  {
    if ($department) {
      $data = Department::where('nama_department', $department)->first();
      return $data->id;
    }
    return 0;
  }

  public function get_tgl_resigned($id)
  {
    $data = Karyawan::where('id_karyawan', $id)->first();
    return $data ? $data->tanggal_resigned : 0;
  }

  public function get_jumlah_hari_libur_regsigned($month, $year, $id)
  {
    $data = Payroll::whereMonth('date', 11)
      ->whereYear('date', 2024)
      ->where('status_karyawan', 'Resigned')
      ->first();
    $tgl_resigned = $this->get_tgl_resigned($data->id_karyawan);
    if (!$data) {
      dd('No resigned employee data found for the given month and year.');
    }
    // Ensure $tgl_resigned is a valid date
    if (!$tgl_resigned) {
      dd('No resignation date found for the given employee.');
    }

    // Convert $tgl_resigned to a DateTime object
    $tgl_resigned = \Carbon\Carbon::parse($tgl_resigned);

    $liburNasional = 0;

    $tgl_libur_nasional = Liburnasional::whereMonth('tanggal_mulai_hari_libur', 11)
      ->whereYear('tanggal_mulai_hari_libur', 2024)
      ->get();

    $cx = 0;

    foreach ($tgl_libur_nasional as $tgl) {
      // Convert $tgl->tanggal_mulai_hari_libur to a DateTime object
      $tanggal_libur = Carbon::parse($tgl->tanggal_mulai_hari_libur);

      if ($tgl_resigned->gt($tanggal_libur)) { // Compare using Carbon's `gt` (greater than) method
        $cx++;
      }
    }

    return $cx;
  }



  public function render()
  {

    $idArrTionghoa = [4, 2, 6435];
    // $idKaryawan = Karyawan::where('etnis', 'China')->pluck('id_karyawan')->toArray();
    $idKaryawan = Karyawan::where('etnis', 'China')
      ->whereNotIn('status_karyawan', ['Blacklist', 'Resigned'])
      ->pluck('id_karyawan')
      ->toArray();
    $idKaryawan = array_merge($idKaryawan, $idArrTionghoa);
    foreach ($idKaryawan as $karyawanId) {
      $data = Karyawan::where('id_karyawan', $karyawanId)->first();
      // lakukan sesuatu dengan $data di sini, misalnya simpan ke array
      // contoh:
      $listData[] = $data;
    }

    dd($listData);

    return view('livewire.test');
  }
}
