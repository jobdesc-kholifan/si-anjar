<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Investor\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class PreviewController extends Controller
{

    public function index(Request $req, $directory, $token, $filename)
    {
        try {
            User::all();
            $pathNotFound = storage_path('app/images/not-found.png');

            if($token != md5(env('APP_KEY_VALUE')))
                throw new \Exception(\DBMessages::permissionRequired, \DBCodes::authorizedError);

            $path = storage_path(str_replace('_', '\\', $directory) . DIRECTORY_SEPARATOR . $filename);
            if(!file_exists($path))
                return response()->file($pathNotFound);

            return response()->file($path);
        } catch (\Exception $e) {
            return $this->errorPage($e);
        }
    }
}
