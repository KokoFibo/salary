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
use App\Models\Department;
use App\Models\Liburnasional;
use App\Models\Personnelrequestform;
use App\Models\Placement;
use App\Models\Rekapbackup;
use App\Models\Requester;
use App\Models\Yfrekappresensi;
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

  public function render()
  {
    // $latestDate = Karyawan::max('created_at');
    $latestDate = Carbon::parse(Karyawan::max('created_at'))->toDateString();
    // $highestIdRecord = Karyawan::whereDate('created_at', $latestDate)
    //   ->orderBy('id_karyawan', 'desc')
    //   ->first();
    $highestIdRecord = Karyawan::orderBy('created_at', 'desc')->first();
    // dd($highestIdRecord->id_karyawan);
    dd($highestIdRecord->id_karyawan);






    return view('livewire.test');
  }
}
