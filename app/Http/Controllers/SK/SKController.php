<?php

namespace App\Http\Controllers\SK;

use App\Http\Controllers\Controller;
use App\Models\SK\SK;
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
        ['label' => 'SK', 'active' => true]
    ];


    /* @var SK|Relation */
    protected $SK;

    /* @var Project|Relation */
    protected $project;

    /* @var ProjectSK|Relation */
    protected $projectSK;

    /* @var User|Relation */
    protected $user;

    public function __construct()
    {
        $this->SK = new SK();
        $this->project = new Project();
        $this->projectSK = new projectSK();
        $this->user = new User();
    }

    public function index()
    {
        try {
            $this->breadcrumbs[] = ['label' => 'Investor', 'active' => true];

            return $this->view('sk');
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function datatables()
    {
        try {
            $query = $this->SK->defaultQuery();

            return datatables()->eloquent($query)
                ->editColumn('printed_at', function ($data) {
                    return date('d/m/Y', strtotime($data->printed_at));
                })
                ->addColumn('no_sk', function ($data) {
                    $showDetailSK = new Button("actions.showSK($data->project_id)", Button::btnLinkPrimary, null, 'btn-md');
                    $showDetailSK->setAlign('text-left');
                    $showDetailSK->setLabel($data->no_sk);

                    return $showDetailSK->render();
                })
                ->addColumn('project.project_name', function ($data) {
                    $showDetail = new Button("actions.showProject($data->project_id)", Button::btnLinkPrimary, null, 'btn-md');
                    $showDetail->setAlign('text-left');
                    $showDetail->setLabel($data->project->project_name . "");

                    return $showDetail->render();
                })
                ->addColumn('action', function ($data) {
                    $btnDelete = false;
                    if (findPermission(\DBMenus::investor)->hasAccess(\DBFeature::update))
                        $btnDelete = (new Button("actions.delete($data->id)", Button::btnDanger, Button::btnIconDelete))
                            ->render();

                    $btnEdit = false;
                    if (findPermission(\DBMenus::investor)->hasAccess(\DBFeature::delete))
                        $btnEdit = (new Button("actions.edit($data->id)", Button::btnPrimary, Button::btnIconEdit))
                            ->render();

                    return \DBText::renderAction([$btnEdit, $btnDelete]);
                })
                ->toJson();
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function showSK(Request $req)
    {
        try {
            return response()->json([
                'content' => $this->viewResponse('modal-projectsk'),
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function datatablesSK(Request $req)
    {
        try {
            $id = $req->get('id');
            $query = $this->projectSK->defaultWith($this->projectSK->defaultSelects)
                ->where('project_id', $id);

            return datatables()->eloquent($query)
                ->addColumn('action', function ($data) {

                    $btnPrint = (new Button("actionsProjectSK.print($data->id)", Button::btnPrimary, Button::btnIconPrint))
                        ->render();
                    $btnDetail = (new Button("actionsProjectSK.showDetailProject($data->id)", Button::btnPrimary, Button::btnIconInfo))
                        ->setLabel("Lihat Investor")
                        ->render();

                    return \DBText::renderAction([$btnDetail, $btnPrint]);
                })
                ->toJson();
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

            return response()->json([
                'content' => $this->viewResponse('modal-project', [
                    'project' => $row,
                ]),
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
