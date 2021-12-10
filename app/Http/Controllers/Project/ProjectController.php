<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectPIC;
use Illuminate\Database\Eloquent\Relations\Relation;

class ProjectController extends Controller
{

    protected $viewPath = "projects";
    protected $route = [\DBRoutes::project];
    protected $title = "Proyek";

    protected $breadcrumbs = [
        ['label' => 'Proyek', 'active' => true],
    ];

    /* @var Project|Relation */
    protected $project;

    /* @var ProjectPIC|Relation */
    protected $projectPIC;

    public function __construct()
    {
        $this->project = new Project();
        $this->projectPIC = new ProjectPIC();
    }

    public function index()
    {
        try {
            return $this->view('project');
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function datatables()
    {
        try {
            $query = $this->project->defaultWith($this->project->defaultSelects);

            return datatables()->eloquent($query)
                ->toJson();
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }


    public function create()
    {
        try {


            return $this->view('project-create');
        } catch (\Exception $e) {
            return $this->errorPage($e);
        }
    }
}
