<?php

namespace App\Http\Controllers;

class AppController extends Controller
{

    public function __construct()
    {

    }

    public function index()
    {
        try {

            return $this->view('home');
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}
