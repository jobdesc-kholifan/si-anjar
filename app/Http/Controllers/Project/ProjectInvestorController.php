<?php

namespace App\Http\Controllers\Project;

use App\Helpers\Collections\Projects\ProjectCollection;
use App\Http\Controllers\Controller;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectInvestor;
use App\Models\Projects\ProjectSK;
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

    /* @var ProjectSK|Relation */
    protected $projectSK;

    /* @var ProjectInvestor|Relation */
    protected $projectInvestor;

    public function __construct()
    {
        $this->project = new Project();
        $this->projectSK = new ProjectSK();
        $this->projectInvestor = new ProjectInvestor();
    }

    public function all(Request $req, $projectId)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::view);

            $skId = $this->projectSK->getLatestId($projectId, $req->get('isDraft', false));
            $query = $this->projectInvestor->defaultWith($this->projectInvestor->defaultSelects)
                ->where('project_id', $projectId)
                ->where('project_sk_id', $skId);

            return $this->jsonData($query->get());
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function index($projectId)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::view);

            $row = ProjectCollection::find($projectId);

            $count = $this->projectInvestor->where('project_id', $projectId)
                ->count();

            $skId = $this->projectSK->getLatestId($projectId, false);

            $noSK = "";
            if(!is_null($skId)) {
                $sk = $this->projectSK->find($skId);
                $noSK = "Revision - $sk->revision";
            }

            return $this->view(is_null($skId) ? 'project-tab-investor' : 'project-tab-investor-readonly', [
                'projectId' => $projectId,
                'project' => $row,
                'tabActive' => 'investor',
                'noSK' => $noSK,
                'isDraft' => $count > 0 || !is_null($skId),
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function datatables(Request $req, $projectId)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::view);

            $skId = $req->get('sk_id');
            if(!$req->has('sk_id'))
                $skId = $this->projectSK->getLatestId($projectId, false);

            $query = $this->projectInvestor->defaultWith($this->projectInvestor->defaultSelects)
                ->where('project_id', $projectId)
                ->where('project_sk_id', $skId);

            return datatables()->eloquent($query)
                ->addColumn('shares_value', function($row) {
                    return number_format($row->shares_value, 0, ",", ".") . " Lembar";
                })
                ->addColumn('investment_value', function($row) {
                    return (new IDRLabel($row->investment_value))->render();
                })
                ->toJson();
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function form($projectId)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::view);

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

    public function draft($projectId)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::view);

            $row = ProjectCollection::find($projectId);

            return $this->view( 'project-tab-investor', [
                'projectId' => $projectId,
                'project' => $row,
                'tabActive' => 'investor',
                'isDraft' => true,
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function store(Request $req, $projectId)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::create);

            DB::beginTransaction();

            $isDraft = $req->get('isDraft');

            $project = ProjectCollection::find($projectId);

            $skId = $this->projectSK->getLatestId($projectId);

            if(!is_null($skId)) {
                $this->projectInvestor->where('project_sk_id', $skId)
                    ->delete();
                $this->projectSK->find($skId)->delete();
            }

            $revision = $this->projectSK->lastRevision($projectId);
            $sk = $this->projectSK->create([
                'project_id' => $projectId,
                'revision' => $revision,
                'no_sk' => sprintf("SK-%s", $project->getCode()),
                'is_draft' => $isDraft,
            ]);
            $skId = $sk->id;

            $investors = json_decode($req->get('investors', '[]'));

            $insertInvestor = [];
            foreach($investors as $investor) {

                $insertInvestor[] = collect($investor)->only($this->projectInvestor->getFillable())
                    ->merge([
                        'project_id' => $projectId,
                        'project_sk_id' => $skId,
                        'created_at' => currentDate(),
                        'updated_at' => currentDate(),
                    ])->toArray();
            }

            $this->projectInvestor->insert($insertInvestor);

            if(!$isDraft)
                $this->project->updateModal($projectId);

            DB::commit();

            return $this->jsonSuccess(\DBMessages::successCreate);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonError($e);
        }
    }

    public function show($projectId, $id)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::view);

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
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::update);

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
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::delete);

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
