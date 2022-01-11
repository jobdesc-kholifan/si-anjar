<?php

namespace App\Documents\Excel;

use App\Helpers\Collections\Projects\ProjectCollection;
use App\Helpers\Collections\Projects\ProjectSurkasCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExportSurkas implements FromView
{

    /* @var ProjectCollection */
    protected $project;

    /* @var ProjectSurkasCollection */
    protected $surkas;

    public function setDataProject($project)
    {
        $this->project = $project;
    }

    public function setDataSurkas($surkas)
    {
        $this->surkas = $surkas;
    }

    public function view(): View
    {
        return view('exports.surkas', [
            'project' => $this->project,
            'surkas' => $this->surkas,
        ]);
    }
}
