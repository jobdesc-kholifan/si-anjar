<?php

namespace App\Http\Controllers;

use App\Helpers\Collections\Users\UserCollection;
use App\Models\Authorization\Privilege;
use App\Models\Authorization\User;
use App\Models\Menus\Menu;
use App\Models\Menus\MenuFeature;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    protected $viewPath = "auth";

    /* @var User|Relation */
    protected $user;

    /* @var Menu|Relation */
    protected $menu;

    public function __construct()
    {
        $this->user = new User();
        $this->menu = new Menu();
    }

    public function login()
    {
        try {

            return $this->view('login');
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function processLogin(Request $req)
    {
        try {
            $credentials = [
                'user_name' => $req->get('username'),
                'password' => $req->get('password'),
            ];

            if(!Auth::attempt($credentials))
                throw new \Exception(\DBMessages::loginFailed, \DBCodes::authorizedError);

            $user = new UserCollection(\auth()->user());

            $menus = $this->menu->select(['id', 'parent_id', 'name', 'icon', 'slug', 'sequence'])
                ->with([
                    'privileges' => function($query) use ($user) {
                        Privilege::foreignWith($query)
                            ->with([
                                'menu_feature' => function($query) use ($user) {
                                    MenuFeature::foreignWith($query);
                                }
                            ])
                            ->addSelect('role_id', 'has_access', 'menu_feature_id', 'menu_id')
                            ->where('role_id', $user->getRoleId());
                    }
                ])
                ->whereHas('privileges', function($query) use ($user) {
                    /* @var Relation $query */
                    $query->where('role_id', $user->getRoleId());
                })
                ->get();

            session()->put('menu', $menus->toArray());

            return $this->jsonSuccess(\DBMessages::loginSuccess);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
