<?php

namespace App\Livewire;

use Livewire\Component;

class DeveloperDashboard extends Component
{

    public function delete_failed_jobs()
    {
        delete_failed_jobs();
        $this->dispatch(
            'message',
            type: 'success',
            title: 'Failed Jobs Deleted',
            position: 'center'
        );
    }

    public function clear_payroll_rebuild()
    {
        clear_payroll_rebuild();
        $this->dispatch(
            'message',
            type: 'success',
            title: 'Payrol rebuild Clear',
            position: 'center'
        );
    }
    public function clear_build()
    {
        clear_build();
        $this->dispatch(
            'message',
            type: 'success',
            title: 'Build Clear',
            position: 'center'
        );
    }
    public function render()
    {
        return view('livewire.developer-dashboard');
    }
}
