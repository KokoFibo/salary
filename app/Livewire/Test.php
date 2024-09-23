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
    dd('stop');

    // $payroll = Payroll::whereYear('date', 2024)->whereMonth('date', 3)->get();
    // $this->cx = 0;
    // foreach ($payroll as $d) {
    //   $karyawan = Karyawan::where('id_karyawan', $d->id_karyawan)->first();
    //   if ($karyawan) {
    //     $d->departemen = nama_department($karyawan->department_id);
    //     $d->save();
    //     $this->cx++;
    //   }
    // }

    // dd($this->cx);


    return view('livewire.test');
  }
}
