<?php

namespace App\Http\Controllers;

use App\Models\Investors\Investor;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectSurkas;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    /* @var Investor|Relation */
    protected $investor;

    /* @var Project|Relation */
    protected $project;

    /* @var ProjectSurkas|Relation */
    protected $projectSurkas;

    public function investor()
    {
        try {
            $this->investor = new Investor();

            return $this->jsonData([
                'value' => IDR($this->investor->count(), '')
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function project()
    {
        try {
            $this->project = new Project();

            return $this->jsonData([
                'value' => IDR($this->project->count(), '')
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function surkas()
    {
        try {
            $this->projectSurkas = new ProjectSurkas();

            $query = $this->projectSurkas->select(DB::raw('SUM(surkas_value) as amount'))
                ->whereMonth('surkas_date', date('m'))
                ->first();

            return $this->jsonData([
                'value' => IDR($query->amount)
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
