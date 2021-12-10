<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Projects\ProjectInvestor;
use Illuminate\Database\Eloquent\Relations\Relation;

class ProjectInvestorController extends Controller
{

    protected $viewPath = "projects";

    /* @var ProjectInvestor|Relation */
    protected $projectInvestor;

    public function __construct()
    {
        $this->projectInvestor = new ProjectInvestor();
    }

    public function datatables()
    {
        try {
            $query = $this->projectInvestor->defaultWith($this->projectInvestor->defaultSelects);

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
                'content' => $this->viewResponse('project-create-investor-form')
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
