<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\Masters\TypesController;
use App\Http\Controllers\Masters\UsersController;
use App\Http\Controllers\Security\MenuController;
use App\Http\Controllers\Security\MenuFeatureController;
use App\Http\Controllers\Security\PrivilegesController;
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

Route::get('/', [AppController::class, 'index']);

Route::group(['prefix' => 'masters'], function() {

    Route::group(['prefix' => 'users'], function() {
        Route::get('select', [UsersController::class, 'select'])->name(DBRoutes::mastersUsersSelect);
        Route::post('datatables', [UsersController::class, 'datatables']);
        Route::get('form', [UsersController::class, 'form']);
        Route::get('detail', [UsersController::class, 'detail']);
        Route::post('multiple-delete', [UsersController::class, 'multipleDelete']);

        Route::get('', [UsersController::class, 'index'])->name(DBRoutes::mastersUsers);
        Route::post('', [UsersController::class, 'store']);
        Route::get('{id}', [UsersController::class, 'show']);
        Route::post('{id}', [UsersController::class, 'update']);
        Route::delete('{id}', [UsersController::class, 'destroy']);
    });

    Route::group(['prefix' => 'type'], function() {
        Route::get('select', [TypesController::class, 'select'])->name(DBRoutes::mastersTypesSelect);

        Route::group(['prefix' => '{slug}'], function() {
            Route::post('datatables', [TypesController::class, 'datatables']);
            Route::get('form', [TypesController::class, 'form']);

            Route::get('', [TypesController::class, 'index'])->name(DBRoutes::mastersTypes);
            Route::post('', [TypesController::class, 'store']);
            Route::get('{id}', [TypesController::class, 'show']);
            Route::post('{id}', [TypesController::class, 'update']);
            Route::delete('{id}', [TypesController::class, 'destroy']);
        });
    });
});

Route::group(['prefix' => 'security'], function() {

    Route::group(['prefix' => 'menu'], function() {
        Route::post('datatables', [MenuController::class, 'datatables']);
        Route::get('form', [MenuController::class, 'form']);
        Route::get('select', [MenuController::class, 'select'])->name(DBRoutes::securityMenuSelect);

        Route::get('', [MenuController::class, 'index'])->name(DBRoutes::securityMenu);
        Route::post('', [MenuController::class, 'store']);
        Route::get('{id}', [MenuController::class, 'edit']);
        Route::post('{id}', [MenuController::class, 'update']);
        Route::delete('{id}', [MenuController::class, 'destroy']);

        Route::group(['prefix' => '{id}/features'], function() {
            Route::post('datatables', [MenuFeatureController::class, 'datatables']);
            Route::get('form', [MenuFeatureController::class, 'form']);

            Route::post('', [MenuFeatureController::class, 'store']);
            Route::get('{featureId}', [MenuFeatureController::class, 'show']);
            Route::post('{featureId}', [MenuFeatureController::class,'update']);
            Route::delete('{featureId}', [MenuFeatureController::class, 'destroy']);
        });
    });

    Route::group(['prefix' => 'privileges'], function() {
        Route::get('features', [PrivilegesController::class, 'features']);

        Route::get('', [PrivilegesController::class, 'index'])->name(DBRoutes::securityPrivileges);
        Route::post('', [PrivilegesController::class, 'update']);
    });
});
