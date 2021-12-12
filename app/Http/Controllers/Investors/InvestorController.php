<?php

namespace App\Http\Controllers\Investors;

use App\Helpers\Uploader\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\Investors\Investor;
use App\Models\Investors\InvestorBank;
use App\Models\Masters\File;
use App\View\Components\Button;
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

    public function __construct()
    {
        $this->investor = new Investor();
        $this->investorBank = new InvestorBank();
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
                'no_ktp:No. KTP' => 'required|digits:16',
                'npwp:NPWP' => 'required|max:100',
                'place_of_birth:Tempat Lahir' => 'required|max:100',
                'date_of_birth:Tanggal Lahir' => 'required|date',
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

            $insertInvestor = collect($req->only($this->investor->getFillable()));
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
                $fileNPWP->setReference($types->get(\DBTypes::fileInvestorKTP), $id);
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
}
