<?php

namespace App\Http\Controllers\SK;

use App\Helpers\Collections\Projects\ProjectCollection;
use App\Helpers\Collections\Projects\ProjectSKCollection;
use App\Http\Controllers\Controller;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectSK;
use App\Models\Authorization\User;
use App\Models\Masters\File;
use App\View\Components\Button;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SKController extends Controller
{

    protected $viewPath = 'sk';
    protected $title = "SK";
    protected $route = [\DBRoutes::staticProject, \DBRoutes::SK];

    protected $breadcrumbs = [
        ['label' => 'Proyek', 'link' => \DBMenus::project],
        ['label' => 'Surkas', 'active' => true],
    ];

    /* @var Project|Relation */
    protected $project;

    /* @var ProjectSK|Relation */
    protected $projectSK;

    /* @var User|Relation */
    protected $user;

    public function __construct()
    {
        $this->project = new Project();
        $this->projectSK = new projectSK();
        $this->user = new User();
    }

    public function index()
    {
        try {
            return $this->view('sk');
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function datatables()
    {
        try {
            $query = $this->projectSK->withProject();

            return datatables()->eloquent($query)
                ->editColumn('printed_at', function ($data) {
                    return !empty($data->printed_at) ? dbDate($data->printed_at, 'd/m/Y H:i:s') : null;
                })
                ->addColumn('no_sk', function ($data) {
                    $showDetailSK = new Button("actions.showSK($data->id)", Button::btnLinkPrimary, null, 'btn-md');
                    $showDetailSK->setAlign('text-left');
                    $showDetailSK->setLabel($data->no_sk);

                    return $showDetailSK->render();
                })
                ->addColumn('project.project_name', function ($data) {
                    $showDetail = new Button("actions.showProject($data->project_id)", Button::btnLinkPrimary, null, 'btn-md');
                    $showDetail->setAlign('text-left');
                    $showDetail->setLabel($data->project->project_name);

                    return $showDetail->render();
                })
                ->addColumn('action', function ($data) {
                    $linkPrint = route(\DBRoutes::projectSKPrint, [$data->project_id]);
                    $btnPrint = (new Button("actions.openLink('$linkPrint', '_blank')", Button::btnPrimary, Button::btnIconPrint))
                        ->render();
                    $btnDetail = (new Button("actions.showInvestor($data->project_id, $data->id)", Button::btnPrimary, Button::btnIconInfo))
                        ->setLabel("Lihat Investor")
                        ->render();

                    return \DBText::renderAction([$btnDetail, $btnPrint]);
                })
                ->toJson();
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function showSK(Request $req)
    {
        try {
            $row = $this->projectSK->defaultWith($this->projectSK->defaultSelects)
                ->find($req->get('id'));

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $sk = new ProjectSKCollection($row);

            return response()->json([
                'content' => $this->viewResponse('modal-sk', [
                    'sk' => $sk
                ]),
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function showProject(Request $req)
    {
        try {
            $row = $this->project->defaultQuery()
                ->with([
                    'file_proposal' => function ($query) {
                        File::foreignWith($query)->addSelect([DBImage()]);
                    },
                    'file_bukti_transfer' => function ($query) {
                        File::foreignWith($query)->addSelect([DBImage()]);
                    },
                    'file_attachment' => function ($query) {
                        File::foreignWith($query)->addSelect([DBImage(), 'description']);
                    }
                ])
                ->find($req->get('id'));

            if (is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $project = new ProjectCollection($row);
            $project->getFileAttachment();

            return response()->json([
                'content' => $this->viewResponse('modal-project', [
                    'project' => $project,
                ]),
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
