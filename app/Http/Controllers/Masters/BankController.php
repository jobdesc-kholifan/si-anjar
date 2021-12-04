<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\Bank;
use App\View\Components\Button;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;

class BankController extends Controller
{

    protected $viewPath = "masters.bank";
    protected $title = "Bank";
    protected $route = [\DBMenus::master, \DBMenus::masterBank];

    protected $breadcrumbs = [
        ['label' => 'Master'],
        ['label' => 'Bank', 'active' => true],
    ];

    /* @var Bank|Relation */
    protected $bank;

    public function __construct()
    {
        $this->bank = new Bank();
    }

    public function index()
    {
        try {

            return $this->view('bank');
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function datatables()
    {
        try {

            $query = $this->bank->defaultWith($this->bank->defaultSelects);

            return datatables()->eloquent($query)
                ->addColumn('action', function($data) {

                    $btnEdit = (new Button("actions.edit($data->id)", Button::btnPrimary, Button::btnIconEdit))
                        ->render();
                    $btnDelete = (new Button("actions.delete($data->id)", Button::btnDanger, Button::btnIconDelete))
                        ->render();

                    return implode("", [
                        $btnEdit,
                        $btnDelete,
                    ]);
                })
                ->toJson();
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function form()
    {
        try {

            return response()->json([
                'content' => $this->viewResponse('modal-form'),
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function store(Request $req)
    {
        try {
            $rules = collect([
                'bank_code:Kode Bank' => 'required|max:15',
                'bank_name:Nama Bank' => 'required|max:100',
            ]);

            $this->customValidate($req->all(), $rules->toArray());

            $insertBank = collect($req->only($this->bank->getFillable()));

            $this->bank->create($insertBank->toArray());

            return $this->jsonSuccess(\DBMessages::successCreate);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function show($id)
    {
        try {

            $row = $this->bank->defaultWith($this->bank->defaultSelects)
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
                'bank_code:Kode Bank' => 'required|max:15',
                'bank_name:Nama Bank' => 'required|max:100',
            ]);

            $this->customValidate($req->all(), $rules->toArray());

            $row = $this->bank->find($id);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $updateBank = collect($req->only($this->bank->getFillable()));
            $row->update($updateBank->toArray());

            return $this->jsonSuccess(\DBMessages::successUpdate);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function destroy($id)
    {
        try {
            $row = $this->bank->find($id);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $row->delete();

            return $this->jsonSuccess(\DBMessages::successDelete);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
