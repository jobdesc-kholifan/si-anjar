<?php

namespace App\Http\Controllers\Project;

use App\Helpers\Collections\Projects\ProjectCollection;
use App\Http\Controllers\Controller;
use App\Models\Projects\ProjectSK;
use Illuminate\Database\Eloquent\Relations\Relation;

class ProjectSKController extends Controller
{

    protected $viewPath = "projects";
    protected $title = "SK";
    protected $route = [\DBMenus::project];

    protected $defaultParams = [
        'tab' => 'sk'
    ];

    /* @var ProjectSK|Relation */
    protected $projectSK;

    protected $breadcrumbs = [
        ['label' => 'Project'],
        ['label' => 'SK', 'active' => true]
    ];

    public function __construct()
    {
        $this->projectSK = new ProjectSK();
    }

    public function index($projectId)
    {
        try {

            $project = ProjectCollection::find($projectId);

            return $this->view('project-sk', [
                'projectId' => $projectId,
                'project' => $project,
            ]);
        } catch (\Exception $e) {
            return $this->errorPage($e);
        }
    }

    public function datatables()
    {
        try {
            $query = $this->projectSK->defaultWith($this->projectSK->defaultSelects);

            return datatables()->eloquent($query)
                ->toJson();
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function form()
    {
        try {
            return response()->json([
                'content' => $this->viewResponse('project-tab-sk-form')
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function store()
    {
        try {

        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
