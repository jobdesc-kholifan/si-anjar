<?php

namespace App\Http\Controllers\Project;

use App\Helpers\Uploader\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\Masters\File;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectPIC;
use App\Models\Projects\ProjectSK;
use App\View\Components\Button;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProjectController extends Controller
{

    protected $viewPath = "projects";
    protected $route = [\DBRoutes::project];
    protected $title = "Proyek";

    protected $breadcrumbs = [
        ['label' => 'Proyek', 'active' => true],
    ];

    /* @var Project|Relation */
    protected $project;

    /* @var ProjectSK|Relation */
    protected $projectSK;

    /* @var ProjectPIC|Relation */
    protected $projectPIC;

    public function __construct()
    {
        $this->project = new Project();
        $this->projectSK = new ProjectSK();
        $this->projectPIC = new ProjectPIC();
    }

    /**
     * @return Collection
     * */
    protected function rules()
    {
        return collect([
            'project_name:Nama Proyek' => 'required|max:100',
            'project_category_id:Kategori Proyek' => 'required',
            'project_value:Nilai Proyek' => 'required',
            'project_shares:Lembar Saham' => 'required',
            'start_date:Tanggal Mulai' => 'required',
            'finish_date:Tanggal Berakhir' => 'required',
            'estimate_profit_value:Proyeksi Keuntungan' => 'required',
            'estimate_profit_id:Perhitungan Keuntungan' => 'required',
        ]);
    }

    public function index()
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::view);

            return $this->view('project');
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function datatables()
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::view);

            $query = $this->project->defaultWith($this->project->defaultSelects);

            return datatables()->eloquent($query)
                ->editColumn('project_value', function($data) {
                    return IDR($data->project_value);
                })
                ->editColumn('status', function($data) {
                    $label = 'Belum Dimulai';

                    if(date('Y-m-d') >= dbDate($data->start_date) && date('Y-m-d') <= dbDate($data->finish_date))
                        $label = 'Dalam Pengerjaan';
                    else if(date('Y-m-d') > dbDate($data->finish_date))
                        $label = 'Selesai';

                    return $label;
                })
                ->addColumn('action', function($data) {

                    $btnDelete = false;
                    if(findPermission(\DBMenus::project)->hasAccess(\DBFeature::update))
                        $btnDelete = (new Button("actions.delete($data->id)", Button::btnDanger, Button::btnIconDelete))
                            ->render();

                    $btnEdit = false;
                    if(findPermission(\DBMenus::project)->hasAccess(\DBFeature::delete))
                        $btnEdit = (new Button("actions.edit($data->id)", Button::btnPrimary, Button::btnIconEdit))
                            ->render();

                    return \DBText::renderAction([$btnEdit, $btnDelete]);
                })
                ->toJson();
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }


    public function create(Request $req)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::view);

            return $this->view('project-create', [
                'tab' => $req->get('tab', 'pic'),
                'saveNext' => true,
            ]);
        } catch (\Exception $e) {
            return $this->errorPage($e);
        }
    }

    public function store(Request $req)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::create);

            $this->customValidate($req->all(), $this->rules()->toArray());

            if(!$req->hasFile('file_proposal'))
                throw new \Exception(sprintf(\DBMessages::fieldRequiredFile, 'Proposal Proyek'), \DBCodes::authorizedError);

            DB::beginTransaction();

            $projectCode = sprintf("PRJ%05d", $this->project->lastId());
            $insertProject = collect($req->only($this->project->getFillable()))
                ->merge([
                    'project_code' => $projectCode,
                    'start_date' => dbDate($req->get('start_date')),
                    'finish_date' => dbDate($req->get('finish_date')),
                    'project_value' => dbIDR($req->get('project_value')),
                    'project_shares' => dbIDR($req->get('project_shares')),
                    'estimate_profit_value' => dbIDR($req->get('estimate_profit_value')),
                ]);
            $project = $this->project->create($insertProject->toArray());

            $insertPIC = [];
            $dataPIC = json_decode($req->get('data_pic', '[]'));
            foreach($dataPIC as $pic) {
                if(empty($pic->pic_name))
                    throw new \Exception('Data pic tidak valid, terdapat nama pic yang kosong', \DBCodes::authorizedError);

                if(empty($pic->phone_number))
                    throw new \Exception("Data pic tidak valid, terdapat no hp pic yang kosong", \DBCodes::authorizedError);

                if(empty($pic->address))
                    throw new \Exception("Data pic tidak valid, terdapat alamat pic yang kosong", \DBCodes::authorizedError);

                $insertPIC[] = [
                    'project_id' => $project->id,
                    'pic_name' => $pic->pic_name,
                    'phone_number' => $pic->phone_number,
                    'address' => $pic->address,
                    'created_at' => currentDate(),
                    'updated_at' => currentDate(),
                ];
            }

            $this->projectPIC->insert($insertPIC);

            $types = findConfig()->in([\DBTypes::fileProjectProposal, \DBTypes::fileProjectBuktiTransfer, \DBTypes::fileProjectAttachment]);

            $fileProposal = FileUpload::upload('file_proposal');
            try {
                $fileProposal->setReference($types->get(\DBTypes::fileProjectProposal), $project->id);
                $fileProposal->moveTo('app/dokumen-project', function ($file) {
                    /* @var UploadedFile $file */
                    return sprintf("proposal-%s.%s", date('YmdHis'), $file->getClientOriginalExtension());
                });
                $fileProposal->save();
            } catch (\Exception $e) {
                $fileProposal->rollBack();
                throw new \Exception($e->getMessage(), \DBCodes::authorizedError);
            }

            $fileBuktiTransfer = FileUpload::upload('file_bukti_transfer');
            try {
                $fileBuktiTransfer->setReference($types->get(\DBTypes::fileProjectBuktiTransfer), $project->id);
                $fileBuktiTransfer->moveTo('app/dokumen-project', function ($file) {
                    /* @var UploadedFile $file */
                    return sprintf("buktitf-%s.%s", date('YmdHis'), $file->getClientOriginalExtension());
                });
                $fileBuktiTransfer->save();
            } catch (\Exception $e) {
                $fileBuktiTransfer->rollBack();
                $fileProposal->rollBack();
                throw new \Exception($e->getMessage(), \DBCodes::authorizedError);
            }

            $fileAttachment = FileUpload::upload('file_lampiran');
            try {
                $fileAttachment->setReference($types->get(\DBTypes::fileProjectAttachment), $project->id);
                $fileAttachment->moveTo('app/lampiran-project', function($file, $i) {
                    /* @var UploadedFile $file */
                    return sprintf("lampiran-%s-%s.%s", date('YmdHis'), $i, $file->getClientOriginalExtension());
                });
                $fileAttachment->save();
            } catch (\Exception $e) {
                $fileProposal->rollBack();
                $fileBuktiTransfer->rollBack();
                return $this->jsonError($e);
            }

            DB::commit();

            return $this->jsonSuccess(\DBMessages::successCreate, [
                'redirect' => route(\DBRoutes::projectInvestor, [$project->id]),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonError($e);
        }
    }

    public function show(Request $req, $projectId)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::view);

            if(!$req->ajax())
                throw new \Exception(\DBMessages::pageNotFound, \DBCodes::authorizedError);

            $row = $this->project->defaultQuery()
                ->with([
                    'file_proposal' => function($query) {
                        File::foreignWith($query)->addSelect([DBImage()]);
                    },
                    'file_bukti_transfer' => function($query) {
                        File::foreignWith($query)->addSelect([DBImage()]);
                    },
                    'file_attachment' => function($query) {
                        File::foreignWith($query)->addSelect([DBImage(), 'description']);
                    }
                ])
                ->find($projectId);

            if(is_null($row))
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

            $tab = $req->get('tab', 'pic');
            return $this->view('project-update', [
                'tab' => $tab,
                'tabActive' => $tab,
                'projectId' => $projectId,
                'saveNext' => false,
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function update(Request $req, $projectId)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::update);

            $this->customValidate($req->all(), $this->rules()->toArray());

            DB::beginTransaction();

            $row = $this->project->find($projectId);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $updateProject = collect($req->only($this->project->getFillable()))
                ->merge([
                    'start_date' => dbDate($req->get('start_date')),
                    'finish_date' => dbDate($req->get('finish_date')),
                    'project_value' => dbIDR($req->get('project_value')),
                    'project_shares' => dbIDR($req->get('project_shares')),
                    'estimate_profit_value' => dbIDR($req->get('estimate_profit_value')),
                ]);
            $row->update($updateProject->toArray());

            $insertPIC = [];
            $deletePIC = [];
            $updatePIC = [];

            $dataPIC = json_decode($req->get('data_pic', '[]'));
            foreach($dataPIC as $pic) {
                if(empty($pic->pic_name))
                    throw new \Exception('Data pic tidak valid, terdapat nama pic yang kosong', \DBCodes::authorizedError);

                if(empty($pic->phone_number))
                    throw new \Exception("Data pic tidak valid, terdapat no hp pic yang kosong", \DBCodes::authorizedError);

                if(empty($pic->address))
                    throw new \Exception("Data pic tidak valid, terdapat alamat pic yang kosong", \DBCodes::authorizedError);

                $item = [
                    'project_id' => $row->id,
                    'pic_name' => $pic->pic_name,
                    'phone_number' => $pic->phone_number,
                    'address' => $pic->address,
                ];

                if($pic->deleted) {
                    if(!in_array($pic->id, $deletePIC))
                        $deletePIC[] = $pic->id;
                } else if(!empty($pic->id)) {
                    $updatePIC[$pic->id] = $item;
                } else if($pic->id == 0) {
                    $insertPIC[] = collect($item)
                        ->merge([
                            'created_at' => currentDate(),
                            'updated_at' => currentDate(),
                        ])
                        ->toArray();
                }
            }

            foreach($updatePIC as $id => $value) {
                $this->projectPIC->where('id', $id)
                    ->update($value);
            }

            $this->projectPIC->whereIn('id', $deletePIC)
                ->delete();

            $this->projectPIC->insert($insertPIC);

            $types = findConfig()->in([\DBTypes::fileProjectProposal, \DBTypes::fileProjectBuktiTransfer, \DBTypes::fileProjectAttachment]);

            $fileProposal = FileUpload::upload('file_proposal');
            try {
                $fileProposal->setReference($types->get(\DBTypes::fileProjectProposal), $row->id);
                $fileProposal->moveTo('app/dokumen-project', function ($file) {
                    /* @var UploadedFile $file */
                    return sprintf("proposal-%s.%s", date('YmdHis'), $file->getClientOriginalExtension());
                });
                $fileProposal->update();
            } catch (\Exception $e) {
                $fileProposal->rollBack();
                throw new \Exception($e->getMessage(), \DBCodes::authorizedError);
            }

            $fileBuktiTransfer = FileUpload::upload('file_bukti_transfer');
            try {
                $fileBuktiTransfer->setReference($types->get(\DBTypes::fileProjectBuktiTransfer), $row->id);
                $fileBuktiTransfer->moveTo('app/dokumen-project', function ($file) {
                    /* @var UploadedFile $file */
                    return sprintf("buktitf-%s.%s", date('YmdHis'), $file->getClientOriginalExtension());
                });
                $fileBuktiTransfer->update();
            } catch (\Exception $e) {
                $fileBuktiTransfer->rollBack();
                $fileProposal->rollBack();
                throw new \Exception($e->getMessage(), \DBCodes::authorizedError);
            }

            $fileAttachment = FileUpload::upload('file_lampiran');
            try {
                $fileAttachment->setReference($types->get(\DBTypes::fileProjectAttachment), $row->id);
                $fileAttachment->moveTo('app/lampiran-project', function($file, $i) {
                    /* @var UploadedFile $file */
                    return sprintf("lampiran-%s-%s.%s", date('YmdHis'), $i, $file->getClientOriginalExtension());
                });
                $fileAttachment->update();
            } catch (\Exception $e) {
                $fileProposal->rollBack();
                $fileBuktiTransfer->rollBack();
                return $this->jsonError($e);
            }
            $fileAttachment->updateDesc();

            DB::commit();

            return $this->jsonSuccess(\DBMessages::successUpdate);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function destroy($projectId)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::delete);

            $row = $this->project->find($projectId);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $row->delete();

            return $this->jsonSuccess(\DBMessages::successDelete);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
