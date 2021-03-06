<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\Menus\MenuFeature;
use App\View\Components\Button;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;

class MenuFeatureController extends Controller
{

    protected $viewPath = "security.menus";
    protected $title = "Fitur";

    /* @var MenuFeature|Relation */
    protected $menuFeature;

    public function __construct()
    {
        $this->menuFeature = new MenuFeature();
    }

    public function datatables($id)
    {
        try {
            findPermission(\DBMenus::securityMenu)->hasAccessOrFail(\DBFeature::view);

            $query = $this->menuFeature->defaultWith()
                ->addSelect($this->menuFeature->defaultSelects)
                ->where('menu_id', $id);

            return datatables()->eloquent($query)
                ->addColumn('action', function($data) {

                    $btnEdit = false;
                    if(findPermission(\DBMenus::securityMenu)->hasAccess('update-feature'))
                        $btnEdit = (new Button("actions.edit($data->id)", Button::btnPrimary, Button::btnIconEdit))
                            ->render();

                    $btnDelete = false;
                    if(findPermission(\DBMenus::securityMenu)->hasAccess('delete-feature'))
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
            findPermission(\DBMenus::securityMenu)->hasAccessOrFail(\DBFeature::view);

            return response()->json([
                'content' => $this->viewResponse('modal-form-feature')
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function store(Request $req, $id)
    {
        try {
            findPermission(\DBMenus::securityMenu)->hasAccessOrFail('create-feature');

            $insertFeature = collect($req->only($this->menuFeature->getFillable()))
                ->merge([
                    'menu_id' => $id,
                ])->toArray();
            $this->menuFeature->create($insertFeature);

            return $this->jsonSuccess(\DBMessages::successCreate);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function show($id, $featureId)
    {
        try {
            findPermission(\DBMenus::securityMenu)->hasAccessOrFail(\DBFeature::view);

            $row = $this->menuFeature->find($featureId);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            return $this->jsonData($row);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function update(Request $req, $id, $featureId)
    {
        try {
            findPermission(\DBMenus::securityMenu)->hasAccessOrFail('update-feature');

            $row = $this->menuFeature->find($featureId, ['id']);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $updateFeature = $req->only($this->menuFeature->getFillable());
            $row->update($updateFeature);

            return $this->jsonSuccess(\DBMessages::successUpdate);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function destroy($id, $featureId)
    {
        try {
            findPermission(\DBMenus::securityMenu)->hasAccessOrFail('delete-feature');

            $row = $this->menuFeature->find($featureId);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $row->delete();

            return $this->jsonSuccess(\DBMessages::successDelete);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
