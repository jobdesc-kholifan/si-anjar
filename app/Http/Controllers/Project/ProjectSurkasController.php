<?php

namespace App\Http\Controllers\Project;

use App\Helpers\Uploader\FileUpload;
use App\Helpers\Collections\Projects\ProjectCollection;
use App\Http\Controllers\Controller;
use App\View\Components\Button;
use App\View\Components\IDRLabel;
use App\Models\Projects\ProjectSurkas;
use App\Models\Masters\File;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProjectSurkasController extends Controller
{

    protected $viewPath = 'projects';

    /* @var ProjectSurkas|Relation */
    protected $projectSurkas;

    public function __construct()
    {
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
                'tabActive' => 'surkas'
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
                ->where('project_id', $projectId);

            return datatables()->eloquent($query)
                ->addColumn('surkas_value', function ($row) {
                    return (new IDRLabel($row->surkas_value))->render();
                })
                ->addColumn('action', function ($data) {
                    $btnDelete = false;
                    if(findPermission(\DBMenus::project)->hasAccess(\DBFeature::delete))
                    $btnDelete = (new Button("actionsSurkas.delete($data->id)", Button::btnDanger, Button::btnIconDelete))
                        ->render();

                    $btnEdit = false;
                    if(findPermission(\DBMenus::project)->hasAccess(\DBFeature::update))
                    $btnEdit = (new Button("actionsSurkas.edit($data->id)", Button::btnPrimary, Button::btnIconEdit))
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
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::view);

            $project = ProjectCollection::find($projectId);

            return response()->json([
                'content' => $this->viewResponse('project-tab-surkas-form', [
                    'project' => $project,
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

            $insertSurkas = collect($req->only($this->projectSurkas->getFillable()))
                ->merge([
                    'project_id' => $projectId,
                    'surkas_value' => $surkasValue,
                    'surkas_date' => dbDate($req->get('surkas_date')),
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
}
