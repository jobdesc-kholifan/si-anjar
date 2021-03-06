<?php

namespace App\Http\Controllers\Addresses;

use App\Http\Controllers\Controller;
use App\Models\Addresses\City;
use App\View\Components\Button;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{

    protected $viewPath = 'addresses.city';
    protected $title = 'Kota / Kabupaten';
    protected $route = [\DBMenus::master, \DBMenus::addresses, \DBMenus::addressesCity];

    protected $breadcrumbs = [
        ['label' => 'Master'],
        ['label' => 'Alamat'],
        ['label' => 'Kota / Kabupaten', 'active' => true]
    ];

    /* @var City|Relation */
    protected $city;

    public function __construct()
    {
        $this->city = new City();
    }

    public function select(Request $req)
    {
        try {
            findPermission(\DBMenus::addressesCity)->hasAccessOrFail(\DBFeature::view);

            $searchValue = trim(strtolower($req->get('searchValue')));

            $query = $this->city->defaultWith($this->city->defaultSelects)
                ->where(function($query) use ($searchValue) {
                    /* @var Relation $query */
                    $query->where(DB::raw('TRIM(LOWER(city_name))'), 'like', "%$searchValue%")
                        ->orWhereHas('province', function($query) use ($searchValue) {
                            /* @var Relation $query */
                            $query->where(DB::raw('TRIM(LOWER(province_name))'), 'like', "%$searchValue%");
                        });
                });

            $json = [];
            foreach($query->get() as $db)
                $json[] = ['id' => $db->id, 'text' => $db->city_name];

            return response()->json($json);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function index()
    {
        try {
            findPermission(\DBMenus::addressesCity)->hasAccessOrFail(\DBFeature::view);

            return $this->view('city');
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function datatables()
    {
        try {
            findPermission(\DBMenus::addressesCity)->hasAccessOrFail(\DBFeature::view);

            $query = $this->city->defaultWith($this->city->defaultSelects);

            return datatables()->eloquent($query)
                ->addColumn('action', function($data) {

                    $btnEdit = false;
                    if(findPermission(\DBMenus::addressesCity)->hasAccess(\DBFeature::update))
                        $btnEdit = (new Button("actions.edit($data->id)", Button::btnPrimary, Button::btnIconEdit))
                            ->render();

                    $btnDelete = false;
                    if(findPermission(\DBMenus::addressesCity)->hasAccess(\DBFeature::delete))
                        $btnDelete = (new Button("actions.delete($data->id)", Button::btnDanger, Button::btnIconDelete))
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
            findPermission(\DBMenus::addressesCity)->hasAccessOrFail(\DBFeature::view);

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
            findPermission(\DBMenus::addressesCity)->hasAccessOrFail(\DBFeature::create);

            $insertCity = collect($req->only($this->city->getFillable()));
            $this->city->create($insertCity->toArray());

            return $this->jsonSuccess(\DBMessages::successCreate);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function show($id)
    {
        try {
            findPermission(\DBMenus::addressesCity)->hasAccessOrFail(\DBFeature::view);

            $row = $this->city->defaultWith($this->city->defaultSelects)
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
            findPermission(\DBMenus::addressesCity)->hasAccessOrFail(\DBFeature::update);

            $row = $this->city->find($id);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $updateCity = collect($req->only($this->city->getFillable()));
            $row->update($updateCity->toArray());

            return $this->jsonData($row);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function destroy($id)
    {
        try {
            findPermission(\DBMenus::addressesCity)->hasAccessOrFail(\DBFeature::delete);

            $row = $this->city->defaultWith($this->city->defaultSelects)
                ->find($id);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $row->delete();

            return $this->jsonSuccess(\DBMessages::successDelete);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
