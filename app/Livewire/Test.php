<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Ter;
use App\Models\User;
use App\Models\Payroll;
use Livewire\Component;
use App\Models\Karyawan;
use App\Models\Tambahan;
use App\Models\Jamkerjaid;
use Livewire\WithPagination;
use App\Models\Bonuspotongan;
use App\Models\Company;
use App\Models\Department;
use App\Models\Jabatan;
use App\Models\Liburnasional;
use App\Models\Personnelrequestform;
use App\Models\Placement;
use App\Models\Rekapbackup;
use App\Models\Requester;
use App\Models\Yfrekappresensi;
use Google\Service\YouTube\ThirdPartyLinkStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

  public function render()
  {
    $data = Payroll::all();
    foreach ($data as $d) {
      $d->jabatan_id = $this->getJabatan_id($d->jabatan);
      $d->company_id = $this->getCompany_id($d->company);
      $d->placement_id = $this->getPlacement_id($d->placement);
      $d->department_id = $this->getDepartment_id($d->departemen);
      $d->save();
    }
    dd('done');

    // $data = Karyawan::where('metode_penggajian', null)->get();







    return view('livewire.test', [
      'data' => $data
    ]);
  }
}
