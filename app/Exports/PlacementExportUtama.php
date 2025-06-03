<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PlacementExportUtama implements WithMultipleSheets
{
    protected $selected_company, $selected_placement, $selected_department, $status, $month, $year;

    public function __construct($selected_company, $selected_placement, $selected_department, $status, $month, $year)
    {
        $this->selected_company = $selected_company;
        $this->selected_placement = $selected_placement;
        $this->selected_department = $selected_department;
        $this->status = $status;
        $this->month = $month;
        $this->year = $year;
    }

    public function sheets(): array
    {
        return [
            // new PlacementTKAExport($this->selected_company, $this->selected_placement, $this->selected_department, $this->status, $this->month, $this->year),
            // new PlacementExport($this->selected_company, $this->selected_placement, $this->selected_department, $this->status, $this->month, $this->year),
            new PayrollExport($this->selected_company, $this->selected_placement, $this->selected_department, $this->status, $this->month, $this->year),
            new PlacementTKAExport($this->selected_company, $this->selected_placement, $this->selected_department, $this->status, $this->month, $this->year),
            new PayrollGabunganExport($this->selected_company, $this->selected_placement, $this->selected_department, $this->status, $this->month, $this->year),
        ];
    }
}
