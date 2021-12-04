<?php

namespace App\Http\Controllers\Masters;

use App\Helpers\Collections\Users\UserCollection;
use App\Http\Controllers\Controller;
use App\Models\Authorization\User;
use App\View\Components\Button;
use App\View\Components\Checkbox;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{

    protected $viewPath = 'masters.users';
    protected $route = [
        \DBMenus::master,
        \DBMenus::masterUsers,
    ];

    protected $breadcrumbs = [
        ['label' => 'Masters'],
    ];

    /* @var User|Relation */
    protected $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function index()
    {
        try {
            findPermission(\DBMenus::masterUsers)->hasAccessOrFail(\DBFeature::view);

            $this->title = "Users";
            $this->breadcrumbs[] = ['label' => "Users", 'active' => true];

            return $this->view('user');
        } catch (\Exception $e) {
            return $this->errorPage($e);
        }
    }

    public function select(Request $req)
    {
        try {
            findPermission(\DBMenus::masterUsers)->hasAccessOrFail(\DBFeature::view);

            $searchValue = trim(strtolower($req->get('term')));

            $query = $this->user->withoutRelations() ($this->user->defaultSelects)
                ->where(function($query) use ($searchValue) {
                    /* @var Relation $query */
                    $query->where(DB::raw('TRIM(LOWER(full_name))'), 'like', "%$searchValue%");
                })
                ->whereHas('status', function($query) {
                    /* @var Relation $query */
                    $query->where('slug', \DBTypes::statusActive);
                });

            if($req->has('usertypecd')) {
                $usertypecd = $req->get('usertypecd');
                if(!is_array($usertypecd))
                    $usertypecd = [$usertypecd];

                $query->whereHas('type', function($query) use ($usertypecd) {
                    /* @var Relation $query */
                    $query->whereIn('slug', $usertypecd);
                });
            }

            $json = [];
            foreach ($query->get() as $db) {
                $json[] = ['id' => $db->id, 'text' => $db->full_name];
            }

            return response()->json($json);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function datatables()
    {
        try {
            findPermission(\DBMenus::masterUsers)->hasAccessOrFail(\DBFeature::view);

            $query = $this->user->defaultWith($this->user->defaultSelects);

            return datatables()->eloquent($query)
                ->addColumn('checkbox', function ($data) {
                    $checkbox = new Checkbox(sprintf('checkbox-delete-%s', $data->id), 'checkbox_delete[]');
                    $checkbox->setValue($data->id);
                    $checkbox->setFullName($data->full_name);
                    return $checkbox->render();
                })
                ->addColumn('gender', function($data) {
                    return !is_null($data->gender) ? ['name' => $data->gender->name] : ['name' => '-'];
                })
                ->addColumn('pob_dob', function($data) {
                    return sprintf("%s, %s ", $data->place_of_birth, !is_null($data->date_of_birth) ? date('d F Y', strtotime($data->date_of_birth)) : '-');
                })
                ->addColumn('email_phone', function($data) {
                    return sprintf("%s/%s", $data->email, $data->phone);
                })
                ->addColumn('action', function($data) {

                    $btnEdit = false;
                    if(findPermission(\DBMenus::masterUsers)->hasAccess(\DBFeature::update))
                        $btnEdit = (new Button("actions.edit($data->id)", Button::btnPrimary, Button::btnIconEdit))
                                ->render();

                    $btnDelete = false;
                    if(findPermission(\DBMenus::masterUsers)->hasAccess(\DBFeature::delete))
                        $btnDelete = (new Button("actions.delete($data->id)", Button::btnDanger, Button::btnIconDelete))
                            ->render();

                    $btnDetail = new Button("actions.detail($data->id)", Button::btnInfo, Button::btnIconInfo);
                    $btnDetail->setLabel('Lihat Detail');

                    return \DBText::renderAction([$btnDetail->render(), $btnEdit, $btnDelete]);
                })
                ->toJson();
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function form()
    {
        try {
            findPermission(\DBMenus::masterUsers)->hasAccessOrFail(\DBFeature::view);

            return response()->json([
                'content' => $this->viewResponse('modal-form'),
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function detail(Request $req)
    {
        try {
            findPermission(\DBMenus::masterUsers)->hasAccessOrFail(\DBFeature::view);

            $row = $this->user->defaultWith($this->user->defaultSelects)
                ->find($req->get('id'));

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $user = new UserCollection($row);

            return response()->json([
                'content' => $this->viewResponse('modal-detail',[
                    'user' => $user,
                ]),
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function store(Request $req)
    {
        try {
            findPermission(\DBMenus::masterUsers)->hasAccessOrFail(\DBFeature::create);

            $rules = collect([
                'full_name:Nama Lengkap' => 'required|max:100',
                'user_name:Nama Pengguna' => 'required|max:30',
                'user_password:Kata Sandi' => 'required',
                'role_id:Role' => 'required',
            ]);

            $this->customValidate($req->all(), $rules->toArray());

            DB::beginTransaction();

            $config = findConfig()->in([\DBTypes::statusActive]);

            $insertUser = collect($req->only($this->user->getFillable()))
                ->merge([
                    'status_id' => $config->get(\DBTypes::statusActive)->getId(),
                    'user_password' =>Hash::make($req->get('user_password')),
                ]);

            $this->user->create($insertUser->toArray());

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
            findPermission(\DBMenus::masterUsers)->hasAccessOrFail(\DBFeature::view);

            $row = $this->user->defaultWith($this->user->defaultSelects)
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
            findPermission(\DBMenus::masterUsers)->hasAccessOrFail(\DBFeature::update);

            $row = $this->user->defaultWith($this->user->defaultSelects)
                ->find($id);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $updateUser = collect($req->only($this->user->getFillable()))
                ->filter(function($data) { return $data != ''; });

            if($req->has('user_password'))
                $updateUser->merge(['user_password' => Hash::make($req->get('user_password'))]);

            $row->update($updateUser->toArray());

            return $this->jsonSuccess(\DBMessages::successUpdate);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function destroy($id)
    {
        try {
            findPermission(\DBMenus::masterUsers)->hasAccessOrFail(\DBFeature::delete);

            $row = $this->user->defaultWith($this->user->defaultSelects)
                ->find($id);

            if(is_null($row))
                throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $row->delete();

            return $this->jsonSuccess(\DBMessages::successDelete);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function multipleDelete(Request $req)
    {
        try {
            findPermission(\DBMenus::masterUsers)->hasAccessOrFail(\DBFeature::delete);

            $ids = $req->get('ids');

            $this->user->whereIn('id', $ids)
                ->delete();

            return $this->jsonSuccess(\DBMessages::successDelete);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
