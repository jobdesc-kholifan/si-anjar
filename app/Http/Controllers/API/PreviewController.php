<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PreviewController extends Controller
{

    public function index(Request $req, $directory, $token, $filename)
    {
        try {
            $pathNotFound = storage_path('app/images/not-found.png');

            if($token != env('APP_KEY_VALUE'))
                throw new \Exception(\DBMessages::permissionRequired, \DBCodes::authorizedError);

            $path = storage_path(str_replace('_', '/', $directory) . DIRECTORY_SEPARATOR . $filename);
            if(!file_exists($path))
                return response()->file($pathNotFound);

            return response()->file($path);
        } catch (\Exception $e) {
            return $this->errorPage($e);
        }
    }
}
