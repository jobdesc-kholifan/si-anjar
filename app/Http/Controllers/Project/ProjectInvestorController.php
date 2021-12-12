<?php

namespace App\Http\Controllers\Project;

use App\Helpers\Collections\Projects\ProjectCollection;
use App\Http\Controllers\Controller;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectInvestor;
use App\View\Components\Button;
use App\View\Components\IDRLabel;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProjectInvestorController extends Controller
{

    protected $viewPath = "projects";
    protected $route = [\DBRoutes::project];
    protected $title = "Project Investor";

    protected $breadcrumbs = [
        ['label' => 'Project', 'active' => true],
    ];

    /* @var Project|Relation */
    protected $project;

    /* @var ProjectInvestor|Relation */
    protected $projectInvestor;

    public function __construct()
    {
        $this->project = new Project();
        $this->projectInvestor = new ProjectInvestor();
    }

    public function index($projectId)
    {
        try {

            $row = ProjectCollection::find($projectId);

            return $this->view('project-tab-investor', [
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
            $project = ProjectCollection::find($projectId);

            $query = $this->projectInvestor->defaultWith($this->projectInvestor->defaultSelects)
                ->where('project_id', $projectId);

            return datatables()->eloquent($query)
                ->addColumn('investment_percentage', function($row) use ($project) {
                    return ($row->investment_value/$project->getValue() * 100)."%";
                })
                ->addColumn('investment_value', function($row) {
                    return (new IDRLabel($row->investment_value))->render();
                })
                ->addColumn('action', function($data) {

                    $btnEdit = (new Button("actionsInvestor.edit($data->id)", Button::btnPrimary, Button::btnIconEdit))
                        ->render();

                    $btnDelete = (new Button("actionsInvestor.delete($data->id)", Button::btnDanger, Button::btnIconDelete))
                        ->render();

                    return \DBText::renderAction([$btnEdit, $btnDelete]);
                })
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

    public function store(Request $req, $projectId)
    {
        try {

            $project = ProjectCollection::find($projectId);
            $modalValue = dbIDR($req->get('investment_value'));

            if($project->getModalValue() + $modalValue > $project->getValue())
                throw new \Exception(sprintf("Modal tidak boleh lebih dari %s", IDR($project->getValue() - $project->getModalValue())), \DBCodes::authorizedError);

            $insertProjectInvestor = collect($req->only($this->projectInvestor->getFillable()))
                ->merge([
                    'project_id' => $projectId,
                    'investment_value' => $modalValue
                ]);
            $this->projectInvestor->create($insertProjectInvestor->toArray());

            $this->project->updateModal($projectId);

            return $this->jsonSuccess(\DBMessages::successCreate);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function show($projectId, $id)
    {
        try {

            $row = $this->projectInvestor->defaultWith($this->projectInvestor->defaultSelects)
                ->find($id);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            return $this->jsonData($row);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function update(Request $req, $projectId, $id)
    {
        try {
            $project = ProjectCollection::find($projectId);

            $row = $this->projectInvestor->defaultWith($this->projectInvestor->defaultSelects)
                ->find($id);

            $currentModalValue = $project->getModalValue() - $row->investment_value;
            $modalValue = dbIDR($req->get('investment_value'));

            if($currentModalValue + $modalValue > $project->getValue())
                throw new \Exception(sprintf("Modal tidak boleh lebih dari %s", IDR($project->getValue() - $currentModalValue)), \DBCodes::authorizedError);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $updateProjectInvestor = collect($req->only($this->projectInvestor->getFillable()))
                ->merge([
                    'investment_value' => dbIDR($req->get('investment_value'))
                ]);
            $row->update($updateProjectInvestor->toArray());

            $this->project->updateModal($projectId);

            return $this->jsonSuccess(\DBMessages::successUpdate);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function destroy($projectId, $id)
    {
        try {

            $row = $this->projectInvestor->find($id);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $row->delete();

            $this->project->updateModal($projectId);

            return $this->jsonSuccess(\DBMessages::successDelete);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
