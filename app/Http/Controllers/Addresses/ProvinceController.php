<?php

namespace App\Http\Controllers\Addresses;

use App\Http\Controllers\Controller;
use App\Models\Addresses\Province;
use App\View\Components\Button;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProvinceController extends Controller
{

    protected $viewPath = "addresses.province";
    protected $title = "Provinsi";
    protected $route = [\DBMenus::master, \DBMenus::addresses, \DBMenus::addressesProvince];

    protected $breadcrumbs = [
        ['label' => 'Master'],
        ['label' => 'Alamat'],
        ['label' => 'Province', 'active' => true],
    ];

    /* @var Province|Relation */
    protected $province;

    public function __construct()
    {
        $this->province = new Province();
    }

    public function select(Request $req)
    {
        try {
            $searchValue = trim(strtolower($req->get('searchValue')));

            $query = $this->province->defaultWith($this->province->defaultSelects)
                ->where(function($query) use ($searchValue) {
                    /* @var Relation $query */
                    $query->where(DB::raw('TRIM(LOWER(province_name))'), 'like', "%$searchValue%");
                });

            $json = [];
            foreach($query->get() as $db)
                $json[] = ['id' => $db->id, 'text' => $db->province_name];

            return response()->json($json);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function index()
    {
        try {

            return $this->view('province');
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function datatables()
    {
        try {
            $query = $this->province->defaultWith($this->province->defaultSelects);

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

    public function store(Request $req)
    {
        try {
            $rules = collect([
                'province_name:Nama Provinsi' => 'required|max:100',
            ]);

            $this->customValidate($req->all(), $rules->toArray());

            $insertProvince = collect($req->only($this->province->getFillable()));
            $this->province->create($insertProvince->toArray());

            return $this->jsonSuccess(\DBMessages::successCreate);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function form()
    {
        try {
            return response()->json([
                'content' => $this->viewResponse('modal-form')
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function show($id)
    {
        try {
            $row = $this->province->defaultWith($this->province->defaultSelects)
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
                'province_name:Nama Provinsi' => 'required|max:100',
            ]);

            $this->customValidate($req->all(), $rules->toArray());

            $row = $this->province->find($id);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $updateProvince = collect($req->only($this->province->getFillable()));
            $row->update($updateProvince->toArray());

            return $this->jsonSuccess(\DBMessages::successUpdate);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function destroy($id)
    {
        try {

            $row = $this->province->find($id);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $row->delete();

            return $this->jsonSuccess(\DBMessages::successDelete);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
