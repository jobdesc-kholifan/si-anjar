<?php

namespace App\Http\Controllers\Project;

use App\Documents\Excel\ExportSurkas;
use App\Helpers\Collections\Projects\ProjectSurkasCollection;
use App\Helpers\Uploader\FileUpload;
use App\Helpers\Collections\Projects\ProjectCollection;
use App\Http\Controllers\Controller;
use App\Models\Investors\Investor;
use App\Models\Investors\InvestorBank;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectInvestor;
use App\Models\Projects\ProjectSK;
use App\View\Components\Button;
use App\View\Components\IDRLabel;
use App\Models\Projects\ProjectSurkas;
use App\Models\Masters\File;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProjectSurkasController extends Controller
{

    protected $title = "Surkas";
    protected $viewPath = 'projects';
    protected $route = [\DBRoutes::staticProject, \DBRoutes::project];

    protected $breadcrumbs = [
        ['label' => 'Project', 'route' => \DBMenus::project],
        ['label' => 'Surkas', 'active' => true]
    ];

    /* @var Project|Relation */
    protected $project;

    /* @var ProjectSK|Relation */
    protected $projectSK;

    /* @var ProjectSurkas|Relation */
    protected $projectSurkas;

    public function __construct()
    {
        $this->project = new Project();
        $this->projectSK = new ProjectSK();
        $this->projectSurkas = new ProjectSurkas();
    }

    public function index($projectId)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::view);

            $project = ProjectCollection::find($projectId);

            return $this->view('project-tab-surkas', [
                'projectId' => $projectId,
                'project' => $project,
                'tabActive' => 'surkas',
            ]);
        } catch (\Exception $e) {
            return $this->errorPage($e);
        }
    }

    public function datatables($projectId)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::view);

            $query = $this->projectSurkas->defaultWith($this->projectSurkas->defaultSelects)
                ->where('project_id', $projectId)
                ->orderBy('id', 'desc');

            return datatables()->eloquent($query)
                ->addColumn('surkas_value', function ($row) {
                    return (new IDRLabel($row->surkas_value))->render();
                })
                ->addColumn('admin_fee', function ($row) {
                    return (new IDRLabel($row->admin_fee))->render();
                })
                ->editColumn('status', function($data) {
                    return !is_null($data->status) ? ['name' => $data->status->name] : ['name' => '-'];
                })
                ->addColumn('action', function ($data) use ($projectId) {
                    $approved = !is_null($data->status) && $data->status->slug == \DBTypes::statusSurkasApproved;

                    $btnDelete = false;
                    if(!$approved && findPermission(\DBMenus::project)->hasAccess(\DBFeature::delete))
                    $btnDelete = (new Button("actionsSurkas.delete($data->id)", Button::btnDanger, Button::btnIconDelete))
                        ->render();

                    $btnEdit = false;
                    if(!$approved && findPermission(\DBMenus::project)->hasAccess(\DBFeature::update))
                    $btnEdit = (new Button("actionsSurkas.edit($data->id)", Button::btnPrimary, Button::btnIconEdit))
                        ->render();

                    $btnExcel = false;
                    if($approved) {
                        $link = route(\DBRoutes::projectSurkasExportExcel, [$projectId]) . "?id=$data->id";
                        $btnExcel = (new Button("actionsSurkas.openLink('$link')", Button::btnSuccess, Button::btnIconFileExcel))
                            ->setLabel("Export Excel")
                            ->render();
                    }

                    $btnApproved = false;
                    if(!is_null($data->status) && $data->status->slug == \DBTypes::statusSurkasWaiting
                        && findPermission(\DBMenus::project)->hasAccess(\DBFeature::approvedSurkas)) {
                        $btnApproved = (new Button("actionsSurkas.approved($data->id)", Button::btnSuccess, Button::btnIconApproved))
                            ->setLabel("Setujui Surkas")
                            ->render();
                    }

                    $btnDetail = (new Button("actionsSurkas.detail($data->id)", Button::btnPrimary, Button::btnIconInfo))
                        ->setLabel("Lihat Detail")
                        ->render();

                    return \DBText::renderAction([$btnDetail, $btnApproved, $btnExcel, $btnEdit, $btnDelete]);
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
            $surkasID = $this->projectSurkas->lastId();

            return response()->json([
                'content' => $this->viewResponse('project-tab-surkas-form', [
                    'project' => $project,
                    'noSurkas' => sprintf("%05d/SURKAS/%s", $surkasID, date('m/Y'))
                ]),
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function store(Request $req, $projectId)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::create);

            $rules = [
                'surkas_value:Nilai Proyek' => 'required',
                'surkas_date:Tanggal Mulai' => 'required',
            ];

            $this->customValidate($req->all(), $rules);

            $surkasValue = dbIDR($req->get('surkas_value'));

            if (!$req->hasFile('file_lampiran_surkas'))
                throw new \Exception(sprintf(\DBMessages::fieldRequiredFile, 'Lampiran'), \DBCodes::authorizedError);

            DB::beginTransaction();

            $configs = findConfig()->in(\DBTypes::statusSurkasWaiting, \DBTypes::statusSurkasApproved);

            $status = $configs->get(\DBTypes::statusSurkasWaiting);
            if(findPermission(\DBMenus::project)->hasAccess(\DBFeature::approvedSurkas))
                $status = $configs->get(\DBTypes::statusSurkasApproved);

            $insertSurkas = collect($req->only($this->projectSurkas->getFillable()))
                ->merge([
                    'project_id' => $projectId,
                    'surkas_value' => $surkasValue,
                    'surkas_date' => dbDate($req->get('surkas_date')),
                    'status_id' => $status->getId(),
                ]);

            $project = $this->projectSurkas->create($insertSurkas->toArray());

            $types = findConfig()->in([\DBTypes::fileSurkasAttachment]);

            $fileLampiran = FileUpload::upload('file_lampiran_surkas');

            try {
                $fileLampiran->setReference($types->get(\DBTypes::fileSurkasAttachment), $project->id);
                $fileLampiran->moveTo('app/lampiran-surkas', function ($file, $i) {
                    /* @var UploadedFile $file */
                    return sprintf("lampiransurkas-%s-%s.%s", date('YmdHis'), $i, $file->getClientOriginalExtension());
                });
                $fileLampiran->save();
            } catch (\Exception $e) {
                $fileLampiran->rollBack();
                return $this->jsonError($e);
            }

            DB::commit();
            return $this->jsonSuccess(\DBMessages::successCreate);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function show($projectId, $id)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::view);

            $row = $this->projectSurkas->defaultQuery()
                ->with([
                    'file_lampiran_surkas' => function ($query) {
                        File::foreignWith($query)->addSelect([DBImage(), 'description']);
                    }
                ])
                ->find($id);
            if (is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            return $this->jsonData($row);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }


    public function edit(Request $req, $projectId)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::view);

            return $this->view('project-update', [
                'tab' => $req->get('tab', 'pic'),
                'projectId' => $projectId,
                'saveNext' => false,
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function update(Request $req, $projectid, $id)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::update);

            $rules = [
                'surkas_value:Nilai Proyek' => 'required',
                'surkas_date:Tanggal Mulai' => 'required',
            ];

            $this->customValidate($req->all(), $rules);

            DB::beginTransaction();

            $row = $this->projectSurkas->find($id);

            if (is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $updateProject = collect($req->only($this->projectSurkas->getFillable()))
                ->merge([
                    'surkas_value' => dbIDR($req->get('surkas_value')),
                    'surkas_date' => dbDate($req->get('surkas_date')),
                ]);
            $row->update($updateProject->toArray());

            $types = findConfig()->in([\DBTypes::fileSurkasAttachment]);

            $fileLampiran = FileUpload::upload('file_lampiran_surkas');
            try {
                $fileLampiran->setReference($types->get(\DBTypes::fileSurkasAttachment), $row->id);
                $fileLampiran->moveTo('app/lampiran-surkas', function ($file, $i) {
                    /* @var UploadedFile $file */
                    return sprintf("lampiran-%s-%s.%s", date('YmdHis'), $i, $file->getClientOriginalExtension());
                });
                $fileLampiran->update();
            } catch (\Exception $e) {
                return $this->jsonError($e);
            }
            $fileLampiran->updateDesc();

            DB::commit();
            return $this->jsonSuccess(\DBMessages::successUpdate);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function destroy($projectid, $id)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::delete);

            $row = $this->projectSurkas->find($id);

            if (is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $row->delete();

            return $this->jsonSuccess(\DBMessages::successDelete);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function totalSurkas($projectId)
    {
        try {
            $total = $this->projectSurkas->totalSurkas($projectId);

            return $this->jsonData(IDR($total));
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function exportExcel(Request $req, $projectId)
    {
        try {
            $row = $this->projectSurkas->defaultQuery()
                ->find($req->get('id'));

            if (is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $surkas = new ProjectSurkasCollection($row);

            $skId = $this->projectSK->getLatestReleasedId($projectId);

            $queryProject = $this->project->defaultQuery()
                ->with([
                    'data_investor' => function($query) use ($skId) {
                        ProjectInvestor::foreignWith($query)
                            ->with([
                                'investor' => function($query) {
                                    Investor::foreignWith($query)
                                        ->with([
                                            'banks' => function($query) {
                                                InvestorBank::foreignWith($query)
                                                    ->addSelect('investor_id');
                                            }
                                        ]);
                                },
                            ])
                            ->addSelect('project_id')
                            ->where('project_sk_id', $skId);
                    }
                ])
                ->find($projectId);

            if(is_null($queryProject))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $project = new ProjectCollection($queryProject);

            $document = new ExportSurkas();
            $document->setDataProject($project);
            $document->setDataSurkas($surkas);

            return Excel::download($document, 'data-surkas.xlsx');
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function approved(Request $req)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::approvedSurkas);

            $row = $this->projectSurkas->find($req->get('id'));

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $config = findConfig()->in([\DBTypes::statusSurkasApproved]);

            $row->update([
                'status_id' => $config->get(\DBTypes::statusSurkasApproved)->getId(),
            ]);

            return $this->jsonSuccess(\DBMessages::success);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function detail(Request $req)
    {
        try {
            $row = $this->projectSurkas->defaultQuery()
                ->with([
                    'file_lampiran_surkas' => function($query) {
                        File::foreignWith($query)->addSelect(DBImage(), 'description');
                    }
                ])
                ->find($req->get('id'));

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $surkas = new ProjectSurkasCollection($row);

            return response()->json([
                'content' => $this->viewResponse('project-tab-surkas-info', [
                    'surkas' => $surkas
                ])
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
