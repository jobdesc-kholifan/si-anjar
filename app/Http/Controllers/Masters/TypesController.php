<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\Config;
use App\View\Components\Button;
use Exception;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TypesController extends Controller
{

    protected $viewPath = 'masters.types';
    protected $title = 'Jenis Data';

    protected $breadcrumbs = [
        ['masters' => 'Masters'],
    ];

    /* @var Config|Relation */
    protected $type;

    public function __construct()
    {
        $this->type = new Config();
    }

    public function select(Request $req)
    {
        try {
            $searchValue = trim(strtolower($req->get('term')));

            $query = $this->type->defaultWith($this->type->defaultSelects)
                ->where(function($query) use ($searchValue) {
                    /* @var Relation $query */
                    $query->where(DB::raw('TRIM(LOWER(name))'), 'like', "%$searchValue%");
                });

            if($req->has('parent_slug')) {
                $parentSlug = $req->get('parent_slug');
                $query->whereHas('parent', function($query) use ($parentSlug) {
                    /* @var Relation $query */
                    $query->where('slug', $parentSlug);
                });
            }

            if($req->has('slugs')) {
                $slugs = $req->get('slugs');
                $query->whereIn('slug', $slugs);
            }

            $json = [];
            foreach($query->get() as $db)
                $json[] = ['id' => $db->id, 'text' => $db->name];

            return response()->json($json);
        } catch (Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function index($slug)
    {
        try {
            $type = findConfig()->in($slug);

            $this->title = $type->get()->getName();
            $this->route = [\DBMenus::master, "masters/type/$slug"];
            $this->breadcrumbs[] = ['label' => $type->get()->getName(), 'active' => true];

            return $this->view('type', [
                'slug' => $slug,
            ]);
        } catch (Exception $e) {
            return $this->errorPage($e);
        }
    }

    public function datatables($slug)
    {
        try {

            $query = $this->type->defaultWith($this->type->defaultSelects)
                ->whereHas('parent', function($query) use ($slug) {
                    /* @var Relation $query */
                    $query->where('slug', $slug);
                });

            return datatables()->eloquent($query)
                ->addColumn('action', function($data) use ($slug) {
                    $btnDelete = (new Button("actions.delete($data->id)", Button::btnDanger, Button::btnIconDelete))->render();
                    $btnEdit = (new Button("actions.edit($data->id)", Button::btnPrimary, Button::btnIconEdit))->render();

                    return implode("", [$btnEdit,$btnDelete]);
                })
                ->toJson();
        } catch (Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function form($slug)
    {
        try {
            $type = findConfig()->in($slug);

            return response()->json([
                'content' => $this->viewResponse('modal-form', [
                    'type' => $type->get(),
                ]),
            ]);
        } catch (Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function store(Request $req, $slug)
    {
        try {
            $type = findConfig()->in($slug);

            $inserts = collect($req->only($this->type->getFillable()))
                ->merge([
                    'slug' => toSlug($req->get('name')),
                    'parent_id' => $type->get()->getId(),
                ])->toArray();

            $this->type->create($inserts);

            return $this->jsonSuccess(\DBMessages::successCreate);
        } catch (Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function show($slug, $id)
    {
        try {

            $row = $this->type->defaultWith($this->type->defaultSelects)
                ->find($id);

            if(is_null($row))
                throw new Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            return $this->jsonData($row);
        } catch (Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function update(Request $req, $slug, $id)
    {
        try {

            $row = $this->type->find($id, ['id']);

            if(is_null($row))
                throw new Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $updates = collect($req->only($this->type->getFillable()))
                ->merge([
                    'slug' => toSlug($req->get('name')),
                ])->toArray();
            $row->update($updates);

            return $this->jsonSuccess(\DBMessages::successUpdate);
        } catch (Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function destroy($slug, $id)
    {
        try {
            $row = $this->type->find($id, ['id']);

            if(is_null($row))
                throw new Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

            $row->delete();

            return $this->jsonSuccess(\DBMessages::successDelete);
        } catch (Exception $e) {
            return $this->jsonError($e);
        }
    }
}
