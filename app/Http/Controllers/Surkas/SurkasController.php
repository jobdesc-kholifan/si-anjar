<?php

namespace App\Http\Controllers\Surkas;

use App\Http\Controllers\Controller;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectSurkas;
use App\View\Components\Button;
use App\View\Components\IDRLabel;

class SurkasController extends Controller
{

    protected $viewPath = 'surkas';
    protected $title = "Surkas";
    protected $route = [\DBRoutes::staticProject, \DBRoutes::surkas];

    protected $breadcrumbs = [
        ['label' => 'Proyek', 'link' => \DBMenus::project],
        ['label' => 'Surkas', 'active' => true],
    ];

    /**
     * @var ProjectSurkas
     * */
    protected $projectSurkas;

    public function __construct()
    {
        $this->projectSurkas = new ProjectSurkas();
    }

    public function index()
    {
        try {

            return $this->view('surkas');
        } catch (\Exception $e) {
            return $this->errorPage($e);
        }
    }

    public function datatables()
    {
        try {

            $query = $this->projectSurkas->defaultQuery()
                ->with([
                    'project' => function($query) {
                        Project::foreignWith($query, ['project_name']);
                    },
                ])
                ->addSelect('project_id');

            return datatables()->eloquent($query)
                ->addColumn('surkas_value', function ($row) {
                    return (new IDRLabel($row->surkas_value))->render();
                })
                ->addColumn('admin_fee', function ($row) {
                    return (new IDRLabel($row->admin_fee))->render();
                })
                ->editColumn('project.project_name', function ($data) {
                    $showDetail = new Button("actions.showProject($data->project_id)", Button::btnLinkPrimary, null, 'btn-md');
                    $showDetail->setAlign('text-left');
                    $showDetail->setLabel($data->project->project_name);

                    return $showDetail->render();
                })
                ->addColumn('action', function($data) {
                    $approved = !is_null($data->status) && $data->status->slug == \DBTypes::statusSurkasApproved;

                    $btnExcel = false;
                    if($approved) {
                        $link = route(\DBRoutes::projectSurkasExportExcel, [$data->project_id]) . "?id=$data->id";
                        $btnExcel = (new Button("actions.openLink('$link')", Button::btnSuccess, Button::btnIconFileExcel))
                            ->setLabel("Export Excel")
                            ->render();
                    };

                    $btnDetail = (new Button("actions.detail($data->project_id, $data->id)", Button::btnPrimary, Button::btnIconInfo))
                        ->setLabel("Lihat Detail")
                        ->render();

                    return \DBText::renderAction([$btnDetail, $btnExcel]);
                })
                ->toJson();
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
