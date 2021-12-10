<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Projects\ProjectSK;
use Illuminate\Database\Eloquent\Relations\Relation;

class ProjectSKController extends Controller
{

    protected $viewPath = "projects";

    /* @var ProjectSK|Relation */
    protected $projectSK;

    public function __construct()
    {
        $this->projectSK = new ProjectSK();
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
                'content' => $this->viewResponse('project-create-sk-form')
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
