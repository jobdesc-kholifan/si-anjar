<?php

namespace App\Http\Controllers;

class AppController extends Controller
{

    public function index()
    {
        try {

            return $this->view('home');
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function features()
    {
        try {

            $reflection = new \ReflectionClass(\DBFeature::class);

            return $this->jsonData($reflection->getConstants());
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
