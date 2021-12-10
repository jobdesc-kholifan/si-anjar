<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Projects\ProjectSurkas;
use Illuminate\Database\Eloquent\Relations\Relation;

class ProjectSurkasController extends Controller
{

    protected $viewPath = 'projects';

    /* @var ProjectSurkas|Relation */
    protected $projectSurkas;

    public function __construct()
    {
        $this->projectSurkas = new ProjectSurkas();
    }

    public function datatables()
    {
        try {
            $query = $this->projectSurkas->defaultWith($this->projectSurkas->defaultSelects);

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
                'content' => $this->viewResponse('project-create-surkas-form'),
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
