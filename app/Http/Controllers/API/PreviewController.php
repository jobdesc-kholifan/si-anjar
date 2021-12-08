<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class PreviewController extends Controller
{

    public function index(Request $req, $directory)
    {
        try {
            $pathNotFound = storage_path('app/images/not-found.png');
            $token = decrypt($req->get('token'));
            if($token != env('APP_KEY_VALUE'))
                return response()->file($pathNotFound);

            $filename = $req->get('filename');

            $path = storage_path(str_replace('_', '\\', $directory) . DIRECTORY_SEPARATOR . $filename);
            if(!file_exists($path))
                return response()->file($pathNotFound);

            return response()->file($path);
        } catch (\Exception $e) {
            echo $e->getMessage();
            return $this->jsonError($e);
        }
    }
}
