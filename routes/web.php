<?php

use App\Http\Controllers\Addresses\CityController;
use App\Http\Controllers\Addresses\ProvinceController;
use App\Http\Controllers\API\PreviewController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Investors\InvestorController;
use App\Http\Controllers\Masters\BankController;
use App\Http\Controllers\Masters\TypesController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Project\ProjectInvestorController;
use App\Http\Controllers\Project\ProjectSKController;
use App\Http\Controllers\Project\ProjectSurkasController;
use App\Http\Controllers\UsersManagement\UsersController;
use App\Http\Controllers\Security\MenuController;
use App\Http\Controllers\Security\MenuFeatureController;
use App\Http\Controllers\UsersManagement\PrivilegesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'guest'], function () {

    Route::get('login', [AuthController::class, 'login'])->name(DBRoutes::authLogin);
    Route::post('login', [AuthController::class, 'processLogin']);
});

Route::group(['middleware' => 'auth'], function () {

    Route::get('logout', function (Request $req) {

        Auth::logout();

        session()->flush();

        $req->session()->invalidate();

        $req->session()->regenerateToken();

        return redirect('/');
    })->name(DBRoutes::authLogout);

    Route::get('/', [AppController::class, 'index']);
    Route::get('/features', [AppController::class, 'features']);

    Route::group(['prefix' => 'preview'], function () {

        Route::get('{directory}/{token}/view/{filename}', [PreviewController::class, 'index']);
    });

    Route::group(['prefix' => 'masters'], function () {

        Route::group(['prefix' => 'type'], function () {
            Route::get('select', [TypesController::class, 'select'])->name(DBRoutes::mastersTypesSelect);

            Route::group(['prefix' => '{slug}'], function () {
                Route::post('datatables', [TypesController::class, 'datatables']);
                Route::get('form', [TypesController::class, 'form']);

                Route::get('', [TypesController::class, 'index'])->name(DBRoutes::mastersTypes);
                Route::post('', [TypesController::class, 'store']);
                Route::get('{id}', [TypesController::class, 'show']);
                Route::post('{id}', [TypesController::class, 'update']);
                Route::delete('{id}', [TypesController::class, 'destroy']);
            });
        });

        Route::group(['prefix' => 'bank'], function () {
            Route::get('select', [BankController::class, 'select'])->name(DBRoutes::mastersBankSelect);
            Route::post('datatables', [BankController::class, 'datatables']);
            Route::get('form', [BankController::class, 'form']);

            Route::get('', [BankController::class, 'index']);
            Route::post('', [BankController::class, 'store']);
            Route::get('{id}', [BankController::class, 'show']);
            Route::post('{id}', [BankController::class, 'update']);
            Route::delete('{id}', [BankController::class, 'destroy']);
        });
    });

    Route::group(['prefix' => 'addresses'], function () {

        Route::group(['prefix' => 'province'], function () {
            Route::get('select', [ProvinceController::class, 'select'])->name(DBRoutes::addressesProvinceSelect);
            Route::post('datatables', [ProvinceController::class, 'datatables']);
            Route::get('form', [ProvinceController::class, 'form']);

            Route::get('', [ProvinceController::class, 'index']);
            Route::post('', [ProvinceController::class, 'store']);
            Route::get('{id}', [ProvinceController::class, 'show']);
            Route::post('{id}', [ProvinceController::class, 'update']);
            Route::delete('{id}', [ProvinceController::class, 'destroy']);
        });

        Route::group(['prefix' => 'city'], function () {
            Route::get('select', [CityController::class, 'select'])->name(DBRoutes::addressesCitySelect);
            Route::post('datatables', [CityController::class, 'datatables']);
            Route::get('form', [CityController::class, 'form']);

            Route::get('', [CityController::class, 'index']);
            Route::post('', [CityController::class, 'store']);
            Route::get('{id}', [CityController::class, 'show']);
            Route::post('{id}', [CityController::class, 'update']);
            Route::delete('{id}', [CityController::class, 'destroy']);
        });
    });

    Route::group(['prefix' => 'users'], function () {

        Route::group(['prefix' => 'user'], function () {
            Route::get('select', [UsersController::class, 'select'])->name(DBRoutes::usersUserSelect);
            Route::post('datatables', [UsersController::class, 'datatables']);
            Route::get('form', [UsersController::class, 'form']);
            Route::get('detail', [UsersController::class, 'detail']);
            Route::post('multiple-delete', [UsersController::class, 'multipleDelete']);

            Route::get('', [UsersController::class, 'index'])->name(DBRoutes::usersUser);
            Route::post('', [UsersController::class, 'store']);
            Route::get('{id}', [UsersController::class, 'show']);
            Route::post('{id}', [UsersController::class, 'update']);
            Route::delete('{id}', [UsersController::class, 'destroy']);
        });

        Route::group(['prefix' => 'privileges'], function () {
            Route::get('features', [PrivilegesController::class, 'features'])->name(DBRoutes::usersRoleFeatures);
            Route::post('datatables', [PrivilegesController::class, 'datatables']);

            Route::get('', [PrivilegesController::class, 'index'])->name(DBRoutes::usersRole);
            Route::get('form', [PrivilegesController::class, 'form']);
            Route::post('', [PrivilegesController::class, 'store']);
            Route::get('{id}', [PrivilegesController::class, 'edit'])->name(DBRoutes::usersRoleEdit);
            Route::post('{id}', [PrivilegesController::class, 'update']);
            Route::delete('{id}', [PrivilegesController::class, 'destroy']);
        });
    });

    Route::group(['prefix' => 'investor'], function () {
        Route::post('datatables', [InvestorController::class, 'datatables']);
        Route::get('select', [InvestorController::class, 'select'])->name(DBRoutes::investorSelect);

        Route::get('', [InvestorController::class, 'index'])->name(DBRoutes::investor);
        Route::get('form', [InvestorController::class, 'form']);
        Route::post('', [InvestorController::class, 'store']);
        Route::get('{id}', [InvestorController::class, 'show']);
        Route::post('{id}', [InvestorController::class, 'update']);
        Route::delete('{id}', [InvestorController::class, 'destroy']);
    });

    Route::group(['prefix' => 'project'], function () {
        Route::post('datatables', [ProjectController::class, 'datatables']);

        Route::get('', [ProjectController::class, 'index'])->name(DBRoutes::project);
        Route::get('create', [ProjectController::class, 'create'])->name(DBRoutes::projectCreate);
        Route::post('create', [ProjectController::class, 'store']);

        Route::group(['prefix' => '{projectId}'], function () {
            Route::get('', [ProjectController::class, 'show'])->name(DBRoutes::projectShow);
            Route::get('edit', [ProjectController::class, 'edit'])->name(DBRoutes::projectEdit);
            Route::post('edit', [ProjectController::class, 'update']);
            Route::delete('', [ProjectController::class, 'destroy']);

            Route::group(['prefix' => 'investor'], function () {
                Route::post('datatables', [ProjectInvestorController::class, 'datatables']);
                Route::get('all', [ProjectInvestorController::class, 'all'])->name(DBRoutes::projectInvestorAll);

                Route::get('', [ProjectInvestorController::class, 'index'])->name(DBRoutes::projectInvestor);
                Route::get('form', [ProjectInvestorController::class, 'form']);
                Route::post('', [ProjectInvestorController::class, 'store']);
                Route::get('{id}', [ProjectInvestorController::class, 'show']);
                Route::post('{id}', [ProjectInvestorController::class, 'update']);
                Route::delete('{id}', [ProjectInvestorController::class, 'destroy']);
            });

            Route::group(['prefix' => 'sk'], function () {
                Route::post('datatables', [ProjectSKController::class, 'datatables']);
                Route::get('detail', [ProjectSKController::class, 'detail']);

                Route::get('', [ProjectSKController::class, 'index'])->name(DBRoutes::projectSK);
                Route::get('revision', [ProjectSKController::class, 'revision'])->name(DBRoutes::projectSKUpdate);
                Route::post('revision', [ProjectSKController::class, 'store']);
            });

            Route::group(['prefix' => 'surkas'], function () {
                Route::post('datatables', [ProjectSurkasController::class, 'datatables']);

                Route::get('', [ProjectSurkasController::class, 'index'])->name(DBRoutes::projectSurkas);
                Route::get('form', [ProjectSurkasController::class, 'form']);
                Route::post('', [ProjectSurkasController::class, 'store']);

                Route::group(['prefix' => '{id}'], function () {
                    Route::get('', [ProjectSurkasController::class, 'show']);
                    Route::post('', [ProjectSurkasController::class, 'update']);
                    Route::delete('', [ProjectSurkasController::class, 'destroy']);
                });
            });
        });
    });

    Route::group(['prefix' => 'security'], function () {

        Route::group(['prefix' => 'menu'], function () {
            Route::post('datatables', [MenuController::class, 'datatables']);
            Route::get('form', [MenuController::class, 'form']);
            Route::get('select', [MenuController::class, 'select'])->name(DBRoutes::securityMenuSelect);

            Route::get('', [MenuController::class, 'index'])->name(DBRoutes::securityMenu);
            Route::post('', [MenuController::class, 'store']);
            Route::get('{id}', [MenuController::class, 'edit']);
            Route::post('{id}', [MenuController::class, 'update']);
            Route::delete('{id}', [MenuController::class, 'destroy']);

            Route::group(['prefix' => '{id}/features'], function () {
                Route::post('datatables', [MenuFeatureController::class, 'datatables']);
                Route::get('form', [MenuFeatureController::class, 'form']);

                Route::post('', [MenuFeatureController::class, 'store']);
                Route::get('{featureId}', [MenuFeatureController::class, 'show']);
                Route::post('{featureId}', [MenuFeatureController::class, 'update']);
                Route::delete('{featureId}', [MenuFeatureController::class, 'destroy']);
            });
        });
    });
});
