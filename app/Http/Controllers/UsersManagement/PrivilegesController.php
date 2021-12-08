<?php

namespace App\Http\Controllers\UsersManagement;

use App\Helpers\Collections\Config\ConfigCollection;
use App\Helpers\Collections\Menu\PrivilegeCollection;
use App\Http\Controllers\Controller;
use App\Models\Authorization\Privilege;
use App\Models\Masters\Config;
use App\Models\Menus\Menu;
use App\Models\Menus\MenuFeature;
use App\View\Components\Button;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrivilegesController extends Controller
{

    protected $viewPath = "users-management.privileges";
    protected $route = [\DBMenus::users, \DBMenus::usersRole];
    protected $title = "Hak Akses";

    protected $breadcrumbs = [
        ['label' => 'Security'],
        ['label' => 'Hak Akses', 'active' => true],
    ];

    /* @var Config|Relation */
    protected $config;

    /* @var Menu|Relation */
    protected $menu;

    /* @var Privilege|Relation */
    protected $permission;

    public function __construct()
    {
        $this->config = new Config();
        $this->menu = new Menu();
        $this->permission = new Privilege();
    }

    public function index()
    {
        try {
            findPermission(\DBMenus::usersRole)->hasAccessOrFail(\DBFeature::view);

            return $this->view('privileges');
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function datatables()
    {
        try {
            $query = $this->config->defaultWith($this->config->defaultSelects)
                ->whereHas('parent', function($query) {
                    /* @var Relation $query */
                    $query->where('slug', \DBTypes::role);
                });

            return datatables()->eloquent($query)
                ->addColumn('action', function($data) {

                    $btnDelete = false;
                    if(findPermission(\DBMenus::usersRole)->hasAccess(\DBFeature::update))
                        $btnDelete = (new Button("actions.delete($data->id)", Button::btnDanger, Button::btnIconDelete))
                            ->render();

                    $btnEdit = false;
                    if(findPermission(\DBMenus::usersRole)->hasAccess(\DBFeature::delete))
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

            $types = findConfig()->in(\DBTypes::role);

            $insertType = collect($req->only($this->config->getFillable()))
                ->merge([
                    'parent_id' => $types->get(\DBTypes::role)->getId(),
                ]);
            $role = $this->config->create($insertType->toArray());

            return $this->jsonSuccess(\DBMessages::successCreate, [
                'redirect' => route(\DBRoutes::usersRoleEdit, [$role->id]),
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function edit($id)
    {
        try {
            $role = ConfigCollection::find($id);

            if(is_null($role->getId()))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            return $this->view('privileges-edit', [
                'role' => $role
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function features(Request $req)
    {
        try {
            findPermission(\DBMenus::usersRole)->hasAccessOrFail(\DBFeature::view);

            $userTypeId = $req->get('role_id');

            $resultMenu = $this->menu->select('id', 'parent_id')
                ->with([
                    'features' => function($query) {
                        MenuFeature::foreignWith($query)
                            ->addSelect('menu_id');
                    }
                ])
                ->addSelect($this->menu->defaultSelects)
                ->get();

            $resultPrivileges = $this->permission->select('id', 'menu_id', 'menu_feature_id', 'has_access')
                ->where('role_id', $userTypeId)
                ->get();

            $permission = [];
            foreach($resultPrivileges as $privilege) {
                $permission[$privilege->menu_id][$privilege->menu_feature_id] = new PrivilegeCollection($privilege);
            }

            $menus = [];
            foreach($resultMenu as $menu) {
                foreach($menu->features as $feature) {
                    $feature->has_access = false;
                    $feature->access_id = 0;

                    if(isset($permission[$menu->id]) && isset($permission[$menu->id][$feature->id])) {
                        $feature->access_id = $permission[$menu->id][$feature->id]->getId();
                        $feature->has_access = $permission[$menu->id][$feature->id]->hasAccess();
                    }
                }

                $menus[] = [
                    'parent_id' => !is_null($menu->parent_id) ? intval("$menu->parent_id") : null,
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'sequence' => $menu->sequence,
                    'features' => $menu->features,
                ];
            }

            return $this->jsonData($menus);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function update(Request $req, $id)
    {
        try {
            findPermission(\DBMenus::usersRole)->hasAccessOrFail(\DBFeature::update);

            DB::beginTransaction();

            $row = $this->config->find($id);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $updateRole = collect($req->only($this->config->getFillable()));
            $row->update($updateRole->toArray());

            $menus = json_decode($req->get('menus', '[]'));

            $insertAccess = [];
            $updateAccess = [];
            $updateAccessId = [];

            foreach($menus as $menu) {
                foreach($menu->features as $feature) {
                    if($feature->access_id == 0 && $feature->has_access)
                        $insertAccess[] = [
                            'role_id' => $id,
                            'menu_id' => $menu->id,
                            'menu_feature_id' => $feature->id,
                            'has_access' => true,
                            'created_at' => currentDate(),
                            'updated_at' => currentDate(),
                        ];

                    else if($feature->access_id != 0) {
                        $updateAccess[$feature->access_id] = ['has_access' => (bool)$feature->has_access];
                        $updateAccessId[]  =$feature->access_id;
                    }
                }
            }

            DB::enableQueryLog();

            $permissions = $this->permission->whereIn('id', $updateAccessId)
                ->get();

            foreach($permissions as $permission) {
                if(isset($updateAccess[$permission->id]))
                    foreach($updateAccess[$permission->id] as $key => $value)
                        $permission->$key = $value;

                $permission->save();
            }

            $this->permission->insert($insertAccess);

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

            DB::beginTransaction();

            /* @var Config $row */
            $row = $this->config->find($id);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $row->privileges()->delete();
            $row->delete();

            DB::commit();

            return $this->jsonSuccess(\DBMessages::successDelete);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonError($e);
        }
    }
}
