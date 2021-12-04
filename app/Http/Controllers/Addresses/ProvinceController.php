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
            findPermission(\DBMenus::addressesProvince)->hasAccessOrFail(\DBFeature::view);

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
            findPermission(\DBMenus::addressesProvince)->hasAccessOrFail(\DBFeature::view);

            return $this->view('province');
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function datatables()
    {
        try {
            findPermission(\DBMenus::addressesProvince)->hasAccessOrFail(\DBFeature::view);

            $query = $this->province->defaultWith($this->province->defaultSelects);

            return datatables()->eloquent($query)
                ->addColumn('action', function($data) {

                    $btnEdit = false;
                    if(findPermission(\DBMenus::addressesProvince)->hasAccess(\DBFeature::update))
                        $btnEdit = (new Button("actions.edit($data->id)", Button::btnPrimary, Button::btnIconEdit))
                            ->render();

                    $btnDelete = false;
                    if(findPermission(\DBMenus::addressesProvince)->hasAccess(\DBFeature::delete))
                        $btnDelete = (new Button("actions.delete($data->id)", Button::btnDanger, Button::btnIconDelete))
                            ->render();

                    return \DBText::renderAction([$btnEdit, $btnDelete]);
                })
                ->toJson();
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function store(Request $req)
    {
        try {
            findPermission(\DBMenus::addressesProvince)->hasAccessOrFail(\DBFeature::create);

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
            findPermission(\DBMenus::addressesProvince)->hasAccessOrFail(\DBFeature::view);

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
            findPermission(\DBMenus::addressesProvince)->hasAccessOrFail(\DBFeature::view);

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
            findPermission(\DBMenus::addressesProvince)->hasAccessOrFail(\DBFeature::update);

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
            findPermission(\DBMenus::addressesProvince)->hasAccessOrFail(\DBFeature::delete);

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
