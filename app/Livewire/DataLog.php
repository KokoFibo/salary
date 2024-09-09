<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class DataLog extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public function render()
    {

        $activity = Activity::whereIn('event', ['updated', 'deleted'])->orderBy('updated_at', 'DESC')->paginate(10);
        // foreach ($activity as $a) {

        //     $data = json_decode($a->properties);
        //     dd($a->properties['attributes']);
        // }

        return view('livewire.data-log', [
            'activity' => $activity
        ]);
    }
}
