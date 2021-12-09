<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;

class ProjectSKController extends Controller
{

    protected $viewPath = "projects";

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
