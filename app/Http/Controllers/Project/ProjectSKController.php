<?php

namespace App\Http\Controllers\Project;

use App\Documents\PDF\PDFDocumentSK;
use App\Helpers\Collections\Projects\PayloadDocumentSK;
use App\Helpers\Collections\Projects\ProjectCollection;
use App\Helpers\Collections\Projects\ProjectSKCollection;
use App\Helpers\Uploader\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectInvestor;
use App\Models\Projects\ProjectSK;
use App\View\Components\Button;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProjectSKController extends Controller
{

    protected $viewPath = "projects";
    protected $title = "SK";
    protected $route = [\DBRoutes::staticProject, \DBRoutes::project];


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
        ['label' => 'Project', 'route' => \DBRoutes::project],
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

            $query = $this->projectSK->defaultWith($this->projectSK->defaultSelects)
                ->where('project_id', $projectId);

            return datatables()->eloquent($query)
                ->addColumn('action', function($data) use ($projectId) {

                    $btnShowDraft = "";
                    if($data->is_draft) {
                        $link = route(\DBRoutes::projectInvestorDraft, [$projectId]);
                        $btnShowDraft = (new Button("actionsSK.openLink('$link')", Button::btnSecondary, Button::btnIconFile))
                            ->setLabel("Tampilkan Draft")
                            ->render();
                    }

                    $btnPrint = "";
                    if(!$data->is_draft && !is_null($data->status) && $data->status->slug = \DBTypes::statusSKApproved)
                        $btnPrint = (new Button("actionsSK.print($data->id)", Button::btnPrimary, Button::btnIconPrint))
                            ->render();

                    $btnDetail = "";
                    if(!$data->is_draft)
                        $btnDetail = (new Button("actionsSK.detail($data->id)", Button::btnPrimary, Button::btnIconInfo))
                            ->setLabel("Lihat Investor")
                            ->render();

                    $btnApproved = "";
                    if(!$data->is_draft && !is_null($data->status) && $data->status->slug == \DBTypes::statusSKWaiting
                        && findPermission(\DBMenus::project)->hasAccess(\DBFeature::approvedSK))
                        $btnApproved = (new Button("actionsSK.approved($data->id)", Button::btnSuccess, Button::btnIconApproved))
                            ->setLabel("Setujui SK")
                            ->render();

                    return \DBText::renderAction([$btnApproved, $btnShowDraft, $btnDetail, $btnPrint]);
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

            $isDraft = $req->get('isDraft') == 'true';
            $revision = $this->projectSK->lastRevision($projectId) + 1;
            $project = ProjectCollection::find($projectId);

            $config = findConfig()->in([\DBTypes::statusSKWaiting, \DBTypes::statusSKApproved]);

            $status = $config->get(\DBTypes::statusSKWaiting);

            $hasApprovedSK = findPermission(\DBMenus::project)->hasAccess(\DBFeature::approvedSK);
            if($hasApprovedSK)
                $status = $config->get(\DBTypes::statusSKApproved);

            $isNewSk = false;
            $skId = $this->projectSK->getLatestId($projectId, true);
            if(is_null($skId)) {
                $sk = $this->projectSK->create([
                    'project_id' => $projectId,
                    'revision' => $revision,
                    'no_sk' => sprintf("SK-%s-rev%03d", $project->getCode(), $revision),
                    'is_draft' => $isDraft,
                    'status_id' => $status->getId(),
                ]);

                $skId = $sk->id;
                $isNewSk = true;
            }

            $investors = json_decode($req->get('investors', '[]'));

            $deletedInvestor = [];
            $updatedInvestor = [];
            $insertInvestor = [];
            foreach ($investors as $investor) {
                $item = collect($investor)->only($this->projectInvestor->getFillable())
                    ->merge([
                        'project_id' => $projectId,
                        'project_sk_id' => $skId,
                        'created_at' => currentDate(),
                        'updated_at' => currentDate(),
                    ]);

                if(!$isNewSk) {
                    if($investor->id != 0) {
                        if(!$investor->deleted) {
                            $updatedInvestor[$investor->id] = $item->except('created_at')->toArray();
                        } else {
                            if(!in_array($investor->id, $deletedInvestor))
                                $deletedInvestor[] = $investor->id;
                        }
                    } else if(!empty($investor->investor_id)) {
                        $insertInvestor[] = $item->toArray();
                    }
                } else {
                    $insertInvestor[] = $item->toArray();
                }
            }

            $this->projectInvestor->whereIn('id', $deletedInvestor)
                ->delete();

            $this->projectInvestor->insert($insertInvestor);

            foreach($updatedInvestor as $id => $values) {
                $this->projectInvestor->where('id', $id)
                    ->update($values);
            }

            if(!$isDraft) {
                $this->project->updateModal($projectId);
                $this->projectSK->where('id', $skId)
                    ->update(['is_draft' => false]);
            }

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

    public function approved(Request $req)
    {
        try {
            findPermission(\DBMenus::project)->hasAccessOrFail(\DBFeature::approvedSK);

            $row = $this->projectSK->find($req->get('id'));

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $config = findConfig()->in([\DBTypes::statusSKApproved]);

            $row->update([
                'status_id' => $config->get(\DBTypes::statusSKApproved)->getId(),
            ]);

            return $this->jsonSuccess(\DBMessages::success);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function formPrintPDF(Request $req, $projectId)
    {
        try {
            $project = ProjectCollection::find($projectId);

            $row = $this->projectSK->defaultWith($this->projectSK->defaultSelects)
                ->find($req->get('id'));

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $sk = new ProjectSKCollection($row);

            return response()->json([
                'content' => $this->viewResponse('project-tab-sk-print', [
                    'project' => $project,
                    'sk' => $sk
                ]),
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function savePrintPDF(Request $req, $projectId)
    {
        try {
            $skId = $req->get('id');
            $row = $this->projectSK->find($skId);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $sk = new ProjectSKCollection($row);
            $json = collect($req->only(PDFDocumentSK::$structure));
            $json->put('signature', $sk->getPdfPayload()->getSignatureJson());

            if($req->hasFile('file_ttd')) {
                $types = findConfig()->in([\DBTypes::fileSignatureSK]);

                $fileTTD = FileUpload::upload('file_ttd');

                try {
                    $fileTTD->setReference($types->get(\DBTypes::fileSignatureSK), $skId);
                    $fileTTD->moveTo('app/signature/sk', function ($file) use ($skId) {
                        /* @var UploadedFile $file */
                        return sprintf("signature-%s-id.%s", $skId, $file->getClientOriginalExtension());
                    });

                    $json->put('signature', $fileTTD->getFileInfo()->toJson());
                } catch (\Exception $e) {
                    $fileTTD->rollBack();
                    return $this->jsonError($e);
                }
            }

            $row->update([
                'printed_at' => currentDate(),
                'pdf_payload' => json_encode($json->toArray())
            ]);

            return $this->jsonSuccess(\DBMessages::success, [
                'redirect' => route(\DBRoutes::projectSKPrint, [$projectId, $skId]),
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function printPDF($projectId, $skId = null)
    {
        try {

            if(is_null($skId))
                $skId = $this->projectSK->getLatestReleasedId($projectId);

            $row = $this->projectSK->defaultWith($this->projectSK->defaultSelects)
                ->find($skId);

            $sk = new ProjectSKCollection($row);
            $rowProject = $this->project->defaultQuery()
                ->with([
                    'data_investor' => function($query) use ($skId) {
                        ProjectInvestor::foreignWith($query)
                            ->where('project_sk_id', $skId)
                            ->addSelect('project_id');
                    }
                ])
                ->find($projectId);

            if(is_null($rowProject))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $project = new ProjectCollection($rowProject);

            $document = new PDFDocumentSK();
            $document->setProject($project);
            $document->setSK($sk);
            $document->build();
            $document->response();

            return true;
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
