<?php

namespace App\Http\Controllers;

use App\Models\Investors\Investor;
use App\Models\Projects\Project;
use Illuminate\Database\Eloquent\Relations\Relation;

class DashboardController extends Controller
{

    /* @var Investor|Relation */
    protected $investor;

    /* @var Project|Relation */
    protected $project;

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
}
