<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $viewPath;
    protected $route = [];

    protected $title;
    protected $subTitle;
    protected $pageTitle = 'SiAnjar';

    protected $breadcrumbs = array();

    public function view($view, $data = [], $mergetData = [])
    {
        if(!array_key_exists('title', $data))
            $data['title'] = $this->title;

        if(!array_key_exists('subTitle', $data))
            $data['subTitle'] = $this->subTitle;

        if(!array_key_exists('pageTitle', $data))
            $data['pageTitle'] = !empty($this->title) ? "$this->title | $this->pageTitle" : $this->pageTitle;

        if(!array_key_exists('route', $data))
            $data['route'] = $this->route;

        if(!array_key_exists('breadcrumbs', $this->breadcrumbs))
            $data['breadcrumbs'] = $this->breadcrumbs;

        return view("$this->viewPath/$view", $data, $mergetData);
    }

    public function viewResponse($view, $datas = [], $mergeDatas = [])
    {
        $view = $this->view($view, $datas, $mergeDatas);
        return preg_replace('/>\s+</', '><', $view->render());
    }

    public function errorPage(\Exception $e, $trace = false): bool
    {
        if($e->getCode() == \DBCodes::authorizedError) {
            echo "<b>" . $e->getMessage() . "</b>. <a href=\"" . url()->previous() . "\">Kembali</a><br />";
        }

        else if($e->getCode() == \DBCodes::permissionError) {
            echo "<b>" . $e->getMessage() . "</b>. <a href=\"" . url('/') . "\">Kembali ke Beranda</a><br />";
        }

        if($trace)
            print_r($e->getTraceAsString());
        return false;
    }

    /**
     * @param \Exception $e
     * @param string|null $classname
     * @param string|null $function
     * @return JsonResponse
     * */
    public function jsonError(\Exception $e, string $classname = null, string $function = null): JsonResponse
    {

        if(Request::ajax()) {
            $code = intval($e->getCode());
            $message = $e->getMessage();

            if($code == 0)
                $message = \DBMessages::serverError;

            return response()->json([
                'status' => $code,
                'result' => false,
                'message' => $message,
                'code' => $code,
                'reporting' => array(
                    'type' => 'API Web',
                    'filename' => $e->getFile(),
                    'classname' => $classname,
                    'function' => $function,
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                )
            ], $code != \DBCodes::authorizedError ? 500 : 200);
        } else return $this->errorPage($e);
    }

    public function jsonSuccess($message, $data = array()): JsonResponse
    {
        $json['result'] = true;
        $json['status'] = 200;
        $json['message'] = $message;
        $json['data'] = $data;

        return response()->json($json);
    }

    public function jsonData($data = array()): JsonResponse
    {
        $json['result'] = true;
        $json['status'] = 200;
        $json['message'] = '';
        $json['data'] = $data;

        return response()->json($json);
    }

    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @throws \Exception
     */
    public function customValidate(array $data, array $rules, array $messages = array())
    {

        $customRules = array();
        $customAttribtues = array();
        foreach($rules as $attribute => $rule) {
            if(strpos($attribute, ':') !== FALSE) {
                list($attributeRule, $attributeName) = explode(':', $attribute);
                $customRules[$attributeRule] = $rule;
                $customAttribtues[$attributeRule] = $attributeName;
            }

            else {
                $customRules[$attribute] = $rule;
            }
        }

        $validator = Validator::make($data,  $customRules, array_merge([
            'required' => ':attribute tidak boleh kosong',
            'numeric' => ':attribute harus angka',
            'integer' => ':attribute harus angka',
            'max' => ':attribute tidak boleh lebih dari :max karakter',
            'email' => 'Alamat :attribute tidak valid',
            'date' => ':attribute tidak valid sebagai tanggal',
            'date_format' => 'Format tanggal :attribute tidak valid, :attribute harus berformat',
            'digits' => ':attribute harus :value digit',
            'digits_between' => ':attribute harus lebih dari :min digit dan kurang dari :max digit',
        ], $messages));

        if($validator->fails()) {
            $strmessage = '';
            foreach($validator->errors()->messages() as $attribute => $arrmessage) {
                foreach($arrmessage as $message) {
                    if(array_key_exists($attribute, $customAttribtues)) {
                        $finder = str_replace('_', ' ', $attribute);
                        $strmessage .= sprintf("- %s<br />", str_replace($finder, $customAttribtues[$attribute], $message));
                    }

                    else {
                        $strmessage .= sprintf("- %s<br/>", str_replace($attribute, ucfirst($attribute), $message));
                    }
                }
            }

            throw new \Exception($strmessage, \DBCodes::authorizedError);
        }
    }
}
