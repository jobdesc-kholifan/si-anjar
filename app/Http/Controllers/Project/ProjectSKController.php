<?php

namespace App\Http\Controllers\Project;

use App\Helpers\Collections\Projects\ProjectCollection;
use App\Http\Controllers\Controller;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectInvestor;
use App\Models\Projects\ProjectSK;
use App\View\Components\Button;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class ProjectSKController extends Controller
{

    protected $viewPath = "projects";
    protected $title = "SK";
    protected $route = [\DBMenus::project];


    protected $defaultParams = [
        'tab' => 'sk'
    ];

    /* @var Project|Relation */
    protected $project;

    /* @var ProjectInvestor|Relation */
    protected $projectInvestor;

    /* @var ProjectSK|Relation */
    protected $projectSK;

    protected $breadcrumbs = [
        ['label' => 'Project'],
        ['label' => 'SK', 'active' => true]
    ];

    public function __construct()
    {
        $this->project = new Project();
        $this->projectInvestor = new ProjectInvestor();
        $this->projectSK = new ProjectSK();
    }

    public function index($projectId)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::view);

            $project = ProjectCollection::find($projectId);

            $skId = $this->projectSK->getLatestId($projectId, false);
            $countInvestor = $this->projectInvestor->where('project_sk_id', $skId)
                ->count();

            return $this->view('project-tab-sk', [
                'projectId' => $projectId,
                'project' => $project,
                'tabActive' => 'sk',
                'countInvestor' => $countInvestor,
            ]);
        } catch (\Exception $e) {
            return $this->errorPage($e);
        }
    }

    public function datatables($projectId)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::view);

            $skId = $this->projectSK->getLatestId($projectId, true);

            $query = $this->projectSK->defaultWith($this->projectSK->defaultSelects)
                ->where('project_id', $projectId);

            return datatables()->eloquent($query)
                ->addColumn('action', function($data) use ($skId) {

                    $btnShowDraft = "";
                    if($data->is_draft && $data->id == $skId)
                        $btnShowDraft = (new Button("actionsSK.showDraft($data->id)", Button::btnSecondary, Button::btnIconFile))
                            ->setLabel("Tampilkan Draft")
                            ->render();

                    $btnPrint = "";
                    if(!$data->is_draft || $data->id != $skId)
                        $btnPrint = (new Button("actions.print($data->id)", Button::btnPrimary, Button::btnIconPrint))
                            ->render();

                    $btnDetail = "";
                    if(!$data->is_draft || $data->id != $skId)
                        $btnDetail = (new Button("actionsSK.detail($data->id)", Button::btnPrimary, Button::btnIconInfo))
                            ->setLabel("Lihat Investor")
                            ->render();

                    return \DBText::renderAction([$btnShowDraft, $btnDetail, $btnPrint]);
                })
                ->toJson();
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function revision($projectId)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::update);

            $row = ProjectCollection::find($projectId);

            return $this->view('project-tab-investor', [
                'projectId' => $projectId,
                'project' => $row,
                'tabActive' => 'sk',
                'isDraft' => false,
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
            $revision = $this->projectSK->lastRevision($projectId) + 1;
            $project = ProjectCollection::find($projectId);

            $sk = $this->projectSK->create([
                'project_id' => $projectId,
                'revision' => $revision,
                'no_sk' => sprintf("SK-%s-rev%03d", $project->getCode(), $revision),
                'is_draft' => $isDraft,
            ]);

            $investors = json_decode($req->get('investors', '[]'));

            $insertInvestor = [];
            foreach ($investors as $investor) {

                $insertInvestor[] = collect($investor)->only($this->projectInvestor->getFillable())
                    ->merge([
                        'project_id' => $projectId,
                        'project_sk_id' => $sk->id,
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

    public function detail(Request $req, $projectId)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::view);

            $id = $req->get('id');
            $sk = $this->projectSK->defaultWith($this->projectSK->defaultSelects)
                ->find($id);

            if(is_null($sk))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            return response()->json([
                'content' => $this->viewResponse('project-tab-sk-detail', [
                    'sk' => $sk
                ]),
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
