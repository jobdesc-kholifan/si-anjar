<?php

namespace App\Http\Controllers\Investors;

use App\Helpers\Uploader\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\Investors\Investor;
use App\Models\Investors\InvestorBank;
use App\Models\Masters\File;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectInvestor;
use App\View\Components\Button;
use App\View\Components\IDRLabel;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class InvestorController extends Controller
{

    protected $viewPath = 'investors';
    protected $title = "Investor";
    protected $route = [\DBRoutes::investor];

    protected $breadcrumbs = [];

    /* @var Investor|Relation */
    protected $investor;

    /* @var InvestorBank|Relation */
    protected $investorBank;

    /* @var Project|Relation */
    protected $project;

    /* @var ProjectInvestor|Relation */
    protected $projectInvestor;

    public function __construct()
    {
        $this->investor = new Investor();
        $this->investorBank = new InvestorBank();
        $this->project = new Project();
        $this->projectInvestor = new ProjectInvestor();
    }

    public function select(Request $req)
    {
        try {
            $searchValue = trim(strtolower($req->get('term')));
            $query = $this->investor->defaultWith($this->investor->defaultSelects)
                ->where(function($query) use ($searchValue) {
                    /* @var Relation $query */
                    $query->where(DB::raw('TRIM(LOWER(investor_name))'), 'like', "%$searchValue%");
                });

            $json = [];
            foreach($query->get() as $db)
                $json[] = ['id' => $db->id, 'text' => $db->investor_name];

            return response()->json($json);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function search()
    {
        try {


        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function index()
    {
        try {
            $this->breadcrumbs[] = ['label' => 'Investor', 'active' => true];

            return $this->view('investor');
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function datatables()
    {
        try {
            $query = $this->investor->defaultQuery();

            return datatables()->eloquent($query)
                ->editColumn('created_at', function($data) {
                    return date('d/m/Y', strtotime($data->created_at));
                })
                ->addColumn('total_project', function($data) {
                    $showDetail = new Button("actions.showProject($data->id)", Button::btnLinkPrimary, null, 'btn-sm');
                    $showDetail->setAlign('text-center');
                    $showDetail->setLabel($data->count_project . " Proyek");
                    return $showDetail->render();
                })
                ->addColumn('total_investment', function($data) {
                    $showDetail = new Button("actions.showInvestment($data->id)", Button::btnPrimary);
                    $showDetail->setAlign('text-right');
                    $showDetail->setLabel(IDR($data->count_investment));
                    return $showDetail->render();
                })
                ->addColumn('action', function($data) {

                    $btnDelete = false;
                    if(findPermission(\DBMenus::investor)->hasAccess(\DBFeature::update))
                        $btnDelete = (new Button("actions.delete($data->id)", Button::btnDanger, Button::btnIconDelete))
                            ->render();

                    $btnEdit = false;
                    if(findPermission(\DBMenus::investor)->hasAccess(\DBFeature::delete))
                        $btnEdit = (new Button("actions.edit($data->id)", Button::btnPrimary, Button::btnIconEdit))
                            ->render();

                    return \DBText::renderAction([$btnEdit, $btnDelete]);
                })
                ->toJson();
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function form()
    {
        try {
            $types = findConfig()->parentIn(\DBTypes::gender);

            return response()->json([
                'content' => $this->viewResponse('modal-form', [
                    'genders' => $types->children(\DBTypes::gender),
                ])
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function store(Request $req)
    {
        try {
            $rules = collect([
                'investor_name:Nama Investor' => 'required|max:100',
                'email:Email' => 'required|max:100|email',
                'phone_number:No Handphone 1' => 'required|digits_between:12,20',
                'no_ktp:No. KTP' => 'required|max:100',
                'npwp:NPWP' => 'required|max:100',
                'place_of_birth:Tempat Lahir' => 'required|max:100',
                'date_of_birth:Tanggal Lahir' => 'required|date_format:d/m/Y',
                'religion_id:Agama' => 'required',
                'relationship_id:Status Perkawinan' => 'required',
                'gender_id:Jenis Kelamin' => 'required',
                'job_name:Pekerjaan' => 'required|max:100',
                'emergency_name:Nama darurat' => 'required|max:100',
                'emergency_phone_number:No. HP darurat' => 'required|digits_between:12,20',
                'emergency_relationship:Hub Keluarga darurat' => 'required|max:100',
            ]);

            if(!$req->hasFile('file_ktp'))
                throw new \Exception(sprintf(\DBMessages::fieldRequiredFile, 'KTP'), \DBCodes::authorizedError);

            if(!$req->hasFile('file_npwp'))
                throw new \Exception(sprintf(\DBMessages::fieldRequiredFile, 'NPWP'), \DBCodes::authorizedError);

            $this->customValidate($req->all(), $rules->toArray());

            DB::beginTransaction();

            $insertInvestor = collect($req->only($this->investor->getFillable()))
                ->merge([
                    'date_of_birth' => dbDate($req->get('date_of_birth')),
                ]);
            $investor = $this->investor->create($insertInvestor->toArray());

            $insertBanks = [];
            $banks = json_decode($req->get('banks', '[]'));
            foreach ($banks as $bank) {
                if(empty($bank->bank_id))
                    throw new \Exception("Data bank tidak valid, terdapat bank yang kosong", \DBCodes::authorizedError);

                if(empty($bank->branch_name))
                    throw new \Exception("Data bank tidak valid, terdapat nama cabang yang kosong", \DBCodes::authorizedError);

                if(empty($bank->no_rekening))
                    throw new \Exception("Data bank tidak valid, terdapat no rekening yang kosong", \DBCodes::authorizedError);

                if(empty($bank->atas_nama))
                    throw new \Exception("Data bank tidak valid, terdapat atas nama bank yang kosong", \DBCodes::authorizedError);

                $insertBanks[] = [
                    'investor_id' => $investor->id,
                    'bank_id' => $bank->bank_id,
                    'branch_name' => $bank->branch_name,
                    'no_rekening' => $bank->no_rekening,
                    'atas_nama' => $bank->atas_nama,
                    'created_at' => currentDate(),
                    'updated_at' => currentDate(),
                ];
            }

            $this->investorBank->insert($insertBanks);

            $types = findConfig()->in([\DBTypes::fileInvestorKTP, \DBTypes::fileInvestorNPWP]);

            $fileKTP = FileUpload::upload('file_ktp');
            try {
                $fileKTP->setReference($types->get(\DBTypes::fileInvestorKTP), $investor->id);
                $fileKTP->moveTo('app/dokumen-investor', function ($file) {
                    /* @var UploadedFile $file */
                    return sprintf("ktp-%s.%s", date('YmdHis'), $file->getClientOriginalExtension());
                });
                $fileKTP->save();
            } catch (\Exception $e) {
                $fileKTP->rollBack();
                throw new \Exception($e->getMessage(), \DBCodes::authorizedError);
            }

            $fileNPWP = FileUpload::upload('file_npwp');
            try {
                $fileNPWP->setReference($types->get(\DBTypes::fileInvestorNPWP), $investor->id);
                $fileNPWP->moveTo('app/dokumen-investor', function ($file) {
                    /* @var UploadedFile $file */
                    return sprintf("npwp-%s.%s", date('YmdHis'), $file->getClientOriginalExtension());
                });
                $fileNPWP->save();
            } catch (\Exception $e) {
                $fileNPWP->rollBack();
                $fileKTP->rollBack();
                throw new \Exception($e->getMessage(), \DBCodes::authorizedError);
            }

            DB::commit();

            return $this->jsonSuccess(\DBMessages::successCreate);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonError($e);
        }
    }

    public function show($id)
    {
        try {
            $row = $this->investor->defaultQuery()
                ->with([
                    'file_npwp' => function($query) {
                        File::foreignWith($query)->addSelect(DBImage());
                    },
                    'file_ktp' => function($query) {
                        File::foreignWith($query)->addSelect(DBImage());
                    }
                ])
                ->find($id);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            return $this->jsonData($row);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function update(Request $req, $id)
    {
        try {
            $rules = collect([
                'investor_name:Nama Investor' => 'required|max:100',
                'email:Email' => 'required|max:100|email',
                'phone_number:No Handphone 1' => 'required|digits_between:12,20',
                'no_ktp:No. KTP' => 'required|max:100',
                'npwp:NPWP' => 'required|max:100',
                'place_of_birth:Tempat Lahir' => 'required|max:100',
                'date_of_birth:Tanggal Lahir' => 'required|date_format:d/m/Y',
                'religion_id:Agama' => 'required',
                'relationship_id:Status Perkawinan' => 'required',
                'gender_id:Jenis Kelamin' => 'required',
                'job_name:Pekerjaan' => 'required|max:100',
                'emergency_name:Nama darurat' => 'required|max:100',
                'emergency_phone_number:No. HP darurat' => 'required|digits_between:12,20',
                'emergency_relationship:Hub Keluarga darurat' => 'required|max:100',
            ]);

            if(!$req->hasFile('file_ktp'))
                throw new \Exception(sprintf(\DBMessages::fieldRequiredFile, 'KTP'), \DBCodes::authorizedError);

            if(!$req->hasFile('file_npwp'))
                throw new \Exception(sprintf(\DBMessages::fieldRequiredFile, 'NPWP'), \DBCodes::authorizedError);

            $this->customValidate($req->all(), $rules->toArray());

            DB::beginTransaction();

            $row = $this->investor->find($id);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $updateInvestor = collect($req->only($this->investor->getFillable()));
            $row->update($updateInvestor->toArray());

            $deletedId = [];
            $updatedId = [];

            $insertBanks = [];
            $updateBanks = [];

            $banks = json_decode($req->get('banks', '[]'));
            foreach ($banks as $bank) {

                if(!$bank->deleted) {
                    if(empty($bank->bank_id))
                        throw new \Exception("Data bank tidak valid, terdapat bank yang kosong", \DBCodes::authorizedError);

                    if(empty($bank->branch_name))
                        throw new \Exception("Data bank tidak valid, terdapat nama cabang yang kosong", \DBCodes::authorizedError);

                    if(empty($bank->no_rekening))
                        throw new \Exception("Data bank tidak valid, terdapat no rekening yang kosong", \DBCodes::authorizedError);

                    if(empty($bank->atas_nama))
                        throw new \Exception("Data bank tidak valid, terdapat atas nama bank yang kosong", \DBCodes::authorizedError);

                    $item = [
                        'investor_id' => $id,
                        'bank_id' => $bank->bank_id,
                        'branch_name' => $bank->branch_name,
                        'no_rekening' => $bank->no_rekening,
                        'atas_nama' => $bank->atas_nama,
                        'created_at' => currentDate(),
                        'updated_at' => currentDate(),
                    ];

                    if($bank->id == 0)
                        $insertBanks[] = $item;
                    else $updateBanks[$bank->id] = collect($item)->except('created_at', 'updated_at')
                        ->toArray();

                    if(!in_array($bank->id, $updatedId))
                        $updatedId[] = $bank->id;
                }

                else {
                    if(!in_array($bank->id, $deletedId))
                        $deletedId[] = $bank->id;
                }
            }

            $this->investorBank->insert($insertBanks);

            foreach($updateBanks as $bankId => $values) {
                $this->investorBank->where('id', $bankId)
                    ->update($values);
            }

            $types = findConfig()->in([\DBTypes::fileInvestorKTP, \DBTypes::fileInvestorNPWP]);

            $fileKTP = FileUpload::upload('file_ktp');
            try {
                $fileKTP->setReference($types->get(\DBTypes::fileInvestorKTP), $id);
                $fileKTP->moveTo('app/dokumen-investor', function ($file) {
                    /* @var UploadedFile $file */
                    return sprintf("ktp-%s.%s", date('YmdHis'), $file->getClientOriginalExtension());
                });
                $fileKTP->update();
            } catch (\Exception $e) {
                $fileKTP->rollBack();
                throw new \Exception($e->getMessage(), \DBCodes::authorizedError);
            }

            $fileNPWP = FileUpload::upload('file_npwp');
            try {
                $fileNPWP->setReference($types->get(\DBTypes::fileInvestorNPWP), $id);
                $fileNPWP->moveTo('app/dokumen-investor', function ($file) {
                    /* @var UploadedFile $file */
                    return sprintf("npwp-%s.%s", date('YmdHis'), $file->getClientOriginalExtension());
                });
                $fileNPWP->update();
            } catch (\Exception $e) {
                $fileNPWP->rollBack();
                $fileKTP->rollBack();
                throw new \Exception($e->getMessage(), \DBCodes::authorizedError);
            }

            DB::commit();

            return $this->jsonSuccess(\DBMessages::successUpdate);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonError($e);
        }
    }

    public function destroy($id)
    {
        try {

            $row = $this->investor->find($id);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $row->delete();

            return $this->jsonSuccess(\DBMessages::successDelete);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function showProject(Request $req)
    {
        try {
            return response()->json([
                'content' => $this->viewResponse('modal-project'),
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function datatablesProject(Request $req)
    {
        try {
            $id = $req->get('id');
            $query = $this->project->defaultWith($this->project->defaultSelects)
                ->whereHas('data_investor', function($query) use ($id) {
                    /* @var Relation $query */
                    $query->where('investor_id', $id)
                        ->whereRaw('project_sk_id = (
                            SELECT tr_project_sk.id
                            FROM tr_project_sk
                            WHERE tr_project_sk.project_id = tr_project_investor.project_id
                            ORDER BY tr_project_sk.revision DESC
                            LIMIT 1
                        )');
                });

            return datatables()->eloquent($query)
                ->editColumn('project_value', function($data) {
                    return IDR($data->project_value);
                })
                ->editColumn('status', function($data) {
                    $label = 'Belum Dimulai';
                    if($data->start_date > date('Y-m-d'))
                        $label = 'Dalam Pengerjaan';

                    if($data->finish_date > date('Y-m-d'))
                        $label = 'Selesai';

                    return $label;
                })
                ->addColumn('action', function($data) {

                    $btnInfo = false;
                    if(findPermission(\DBMenus::project)->hasAccess(\DBFeature::view))
                        $btnInfo = (new Button("actionsProject.showDetailProject($data->id)", Button::btnPrimary, Button::btnIconInfo))
                            ->setLabel("Lihat Detail")
                            ->render();

                    return \DBText::renderAction([$btnInfo]);
                })
                ->toJson();
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function showInvestment(Request $req)
    {
        try {
            return response()->json([
                'content' => $this->viewResponse('modal-investment'),
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function datatablesInvestment(Request $req)
    {
        try {
            $id = $req->get('id');

            $query = $this->projectInvestor->defaultWith($this->projectInvestor->defaultSelects)
                ->where('investor_id', $id)
                ->whereRaw('project_sk_id = (
                    SELECT tr_project_sk.id
                    FROM tr_project_sk
                    WHERE tr_project_sk.project_id = tr_project_investor.project_id
                    ORDER BY tr_project_sk.revision DESC
                    LIMIT 1
                )')
                ->addSelect('project_id');

            return datatables()->eloquent($query)
                ->addColumn('shares_value', function($row) {
                    return number_format($row->shares_value, 0, ",", ".") . " Lembar";
                })
                ->addColumn('investment_value', function($row) {
                    return (new IDRLabel($row->investment_value))->render();
                })
                ->addColumn('action', function($data) {

                    $btnInfo = false;
                    if(findPermission(\DBMenus::project)->hasAccess(\DBFeature::view))
                        $btnInfo = (new Button("actionsInvestment.showDetailInvestment($data->project_id)", Button::btnPrimary, Button::btnIconInfo))
                            ->setLabel("Lihat Detail")
                            ->render();

                    return \DBText::renderAction([$btnInfo]);
                })
                ->toJson();
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
