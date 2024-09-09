<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class UserLog extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public function render()
    {
        $data = Activity::whereDate('created_at', Carbon::today())->orderBy('created_at', 'desc')->paginate(10);
        $total_logs = Activity::select('description')->distinct('description')->count();
        $today_logs = Activity::whereDate('created_at', Carbon::today())->select('description')->distinct('description')->count();
        $yesterday_log = Activity::whereDate('created_at', Carbon::yesterday())->select('description')->distinct('description')->count();
        $total_created_logs = Activity::select('description')->count();
        $data_activity = Activity::whereDate('created_at', Carbon::today())->orderBy('created_at', 'desc')->get();
        $cx = 0;
        foreach ($data_activity as $d) {
            $contains = Str::contains($d->description, ['Admin', 'Senior Admin', 'Super Admin', 'BOD']);
            $contains2 = Str::contains($d->description, ['10000', '20000', '30000', '40000', '50000']);
            if ($contains && $contains2 == false) $cx++;
        }
        return view('livewire.user-log', compact(['data', 'total_logs', 'today_logs', 'yesterday_log', 'total_created_logs', 'cx']));
    }
}
