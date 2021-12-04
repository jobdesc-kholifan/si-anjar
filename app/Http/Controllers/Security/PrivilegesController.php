<?php

namespace App\Http\Controllers\Security;

use App\Helpers\Collections\Menu\PrivilegeCollection;
use App\Http\Controllers\Controller;
use App\Models\Authorization\Privilege;
use App\Models\Menus\Menu;
use App\Models\Menus\MenuFeature;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrivilegesController extends Controller
{

    protected $viewPath = "security.privileges";
    protected $route = [\DBMenus::security, \DBMenus::securityPrivileges];
    protected $title = "Hak Akses";

    protected $breadcrumbs = [
        ['label' => 'Security'],
        ['label' => 'Hak Akses', 'active' => true],
    ];

    /* @var Menu|Relation */
    protected $menu;

    /* @var Privilege|Relation */
    protected $permission;

    public function __construct()
    {
        $this->menu = new Menu();
        $this->permission = new Privilege();
    }

    public function index()
    {
        try {

            return $this->view('privileges');
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function features(Request $req)
    {
        try {
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

    public function update(Request $req)
    {
        try {

            DB::beginTransaction();

            $userTypeId = $req->get('role_id');
            $menus = json_decode($req->get('menus', '[]'));

            $insertAccess = [];
            $updateAccess = [];
            $updateAccessId = [];

            foreach($menus as $menu) {
                foreach($menu->features as $feature) {
                    if($feature->access_id == 0 && $feature->has_access)
                        $insertAccess[] = [
                            'role_id' => $userTypeId,
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
}
