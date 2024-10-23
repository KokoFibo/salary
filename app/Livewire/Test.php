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
use App\Models\Liburnasional;
use App\Models\Personnelrequestform;
use App\Models\Rekapbackup;
use App\Models\Requester;
use App\Models\Yfrekappresensi;
use Illuminate\Support\Facades\DB;

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
    $this->year = 2024;
    $this->month = 6;
    $rekap = Rekapbackup::whereMonth('date', $this->month)->whereYear('date', $this->year)->count();


    return view('livewire.test', [
      'rekap' => $rekap
    ]);
  }
}
