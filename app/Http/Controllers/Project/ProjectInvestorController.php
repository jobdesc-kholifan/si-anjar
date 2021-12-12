<?php

namespace App\Http\Controllers\Project;

use App\Helpers\Collections\Projects\ProjectCollection;
use App\Http\Controllers\Controller;
use App\Models\Projects\ProjectInvestor;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Hash;

class ProjectInvestorController extends Controller
{

    protected $viewPath = "projects";
    protected $route = [\DBRoutes::project];
    protected $title = "Project Investor";

    protected $breadcrumbs = [
        ['label' => 'Project', 'active' => true],
    ];

    /* @var ProjectInvestor|Relation */
    protected $projectInvestor;

    public function __construct()
    {
        $this->projectInvestor = new ProjectInvestor();
    }

    public function index($projectId)
    {
        try {

            $row = ProjectCollection::find($projectId);

            return $this->view('project-investor', [
                'projectId' => $projectId,
                'project' => $row,
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function datatables($projectId)
    {
        try {
            $query = $this->projectInvestor->defaultWith($this->projectInvestor->defaultSelects);

            return datatables()->eloquent($query)
                ->toJson();
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function form($projectId)
    {
        try {

            $project = ProjectCollection::find($projectId);

            return response()->json([
                'content' => $this->viewResponse('project-tab-investor-form', [
                    'project' => $project
                ])
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
