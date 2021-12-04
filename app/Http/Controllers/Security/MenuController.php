<?php

namespace App\Http\Controllers\Security;

use App\Helpers\Collections\Menu\MenuCollection;
use App\Http\Controllers\Controller;
use App\Models\Menus\Menu;
use App\Models\Menus\MenuFeature;
use App\View\Components\Button;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{

    protected $viewPath = "security.menus";
    protected $title = "Menu";
    protected $route = [\DBMenus::security,\DBMenus::securityMenu];

    protected $breadcrumbs = [
        ['label' => 'Security'],
    ];

    /* @var Menu|Relation */
    protected $menu;

    /* @var MenuFeature|Relation */
    protected $menuFeature;

    public function __construct()
    {
        $this->menu = new Menu();
        $this->menuFeature = new MenuFeature();
    }

    public function index()
    {
        try {
            findPermission(\DBMenus::securityMenu)->hasAccessOrFail(\DBFeature::view);

            $this->breadcrumbs[] = ['label' => 'Menu', 'active' => true];

            return $this->view('menu');
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function select(Request $req)
    {
        try {
            findPermission(\DBMenus::securityMenu)->hasAccessOrFail(\DBFeature::view);

            $searchValue = trim(strtolower($req->get('term')));

            $query = $this->menu->defaultWith()
                ->addSelect(['id', 'name'])
                ->where(function($query) use ($searchValue) {
                    /* @var Relation $query */
                    $query->where(DB::raw('TRIM(LOWER(name))'), 'like', "%$searchValue%");
                })
                ->orderBy('name');

            $json = [];
            foreach($query->get() as $db)
                $json[] = ['id' => $db->id, 'text' => $db->name];

            return response()->json($json);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function datatables()
    {
        try {
            findPermission(\DBMenus::securityMenu)->hasAccessOrFail(\DBFeature::view);

            $query = $this->menu->defaultWith()
                ->with([
                    'parent' => function($query) {
                        Menu::foreignWith($query);
                    }
                ])
                ->addSelect('parent_id')
                ->addSelect($this->menu->defaultSelects);

            return datatables()->eloquent($query)
                ->editColumn('parent', function($data) {
                    return ['name' => !is_null($data->parent) ? $data->parent->name : ''];
                })
                ->addColumn('action', function($data) {

                    $btnEdit = false;
                    if(findPermission(\DBMenus::securityMenu)->hasAccess(\DBFeature::update))
                        $btnEdit = (new Button("actions.edit($data->id)", Button::btnPrimary, Button::btnIconEdit))
                            ->render();

                    $btnDelete = false;
                    if(findPermission(\DBMenus::securityMenu)->hasAccess(\DBFeature::delete))
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
                'content' => $this->viewResponse('modal-form'),
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function store(Request $req)
    {
        try {
            findPermission(\DBMenus::securityMenu)->hasAccessOrFail(\DBFeature::create);

            DB::beginTransaction();

            $insertMenu = $req->only($this->menu->getFillable());
            $menu = $this->menu->create($insertMenu);

            if($req->has('withcrud')) {
                $this->menuFeature->insert([
                    ['menu_id' => $menu->id, 'title' => 'Tampil', 'slug' => \DBFeature::view, 'description' => 'User dapat melihat data', 'created_at' => currentDate(), 'updated_at' => currentDate()],
                    ['menu_id' => $menu->id, 'title' => 'Tambah', 'slug' => \DBFeature::create, 'description' => 'User dapat melakukan penambahan data', 'created_at' => currentDate(), 'updated_at' => currentDate()],
                    ['menu_id' => $menu->id, 'title' => 'Ubah', 'slug' => \DBFeature::update, 'description' => 'User dapat melakukan perubahan data', 'created_at' => currentDate(), 'updated_at' => currentDate()],
                    ['menu_id' => $menu->id, 'title' => 'Hapus', 'slug' => \DBFeature::delete, 'description' => 'User dapat menghapus data', 'created_at' => currentDate(), 'updated_at' => currentDate()],

                ]);
            }

            if($req->has('withexcel')) {
                $this->menuFeature->insert([
                    ['menu_id' => $menu->id, 'title' => 'Import Excel', 'slug' => \DBFeature::importExcel, 'description' => 'User dapat melakukan import data dari excel berdasarkan template yang sudah didownload', 'created_at' => currentDate(), 'updated_at' => currentDate()],
                    ['menu_id' => $menu->id, 'title' => 'Export Excel', 'slug' => \DBFeature::exportExcel, 'description' => 'User dapat melakukan export data ke excel', 'created_at' => currentDate(), 'updated_at' => currentDate()],
                ]);
            }

            DB::commit();

            return $this->jsonSuccess(\DBMessages::successCreate, [
                'id' => $menu->id
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function edit($id)
    {
        try {
            findPermission(\DBMenus::securityMenu)->hasAccessOrFail(\DBFeature::update);

            $this->title = 'Detail Menu';
            $this->breadcrumbs[] = ['label' => 'Menu', 'link' => route(\DBRoutes::securityMenu)];
            $this->breadcrumbs[] = ['label' => 'Detail Menu', 'active'];

            $row = $this->menu->defaultWith()
                ->with([
                    'parent' => function($query) {
                        Menu::foreignWith($query);
                    }
                ])
                ->addSelect('parent_id')
                ->addSelect($this->menu->defaultSelects)
                ->find($id);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            return $this->view('create', [
                'row' => new MenuCollection($row)
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function update(Request $req, $id)
    {
        try {
            findPermission(\DBMenus::securityMenu)->hasAccessOrFail(\DBFeature::update);

            $row = $this->menu->find($id, ['id']);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $updateMenu = $req->only($this->menu->getFillable());
            $row->update($updateMenu);

            return $this->jsonSuccess(\DBMessages::successUpdate);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function destroy($id)
    {
        try {
            findPermission(\DBMenus::securityMenu)->hasAccessOrFail(\DBFeature::delete);

            $row = $this->menu->find($id, ['id']);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $row->delete();

            return $this->jsonSuccess(\DBMessages::successDelete);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
