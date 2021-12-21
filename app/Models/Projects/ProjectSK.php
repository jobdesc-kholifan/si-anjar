<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

class ProjectSK extends Model
{
    use HasFactory;

    protected $table = "tr_project_sk";

    protected $fillable = [
        'project_id',
        'revision',
        'no_sk',
        'printed_at',
        'is_draft'
    ];

    public $defaultSelects = [
        'revision',
        'no_sk',
        'printed_at',
        'is_draft'
    ];

    /**
     * static function yang digunakan ketika memanggil with biar tidak perlu
     * dituliskan lagi
     *
     * @param Relation $query
     * @param array $selects
     *
     * @return Relation
     * */
    static public function foreignWith($query, $selects = null)
    {
        $model = new ProjectSK();
        return $model->defaultWith(is_null($selects) ? $model->defaultSelects : $selects, $query);
    }

    /**
     * function untuk setting default with apa saja yang akan sering dipakai
     * tetapi jangan banyak-banyak karena akan memperngaruhi proses loading page
     *
     * @param Relation|ProjectSK $query
     * @param array $selects
     *
     * @return Relation
     * */
    private function _defaultWith($query, $selects = [])
    {
        return $query->with([
        ])->select('id')->addSelect($selects);
    }

    /**
     * function defaultWith yang digunakan untuk dipanggil public
     *
     * @param array $selects
     * @param Relation|ProjectSK|null $query
     *
     * @return Relation
     * */
    public function defaultWith($selects = [], $query = null)
    {
        return $this->_defaultWith(is_null($query) ? $this : $query, $selects);
    }

    public function lastRevision($projectId)
    {
        /* @var Relation $this */
        $row = $this->select(DB::raw('MAX(revision) last_revision'))
            ->where('project_id', $projectId)
            ->first();

        return !is_null($row->last_revision) ? $row->last_revision : 0;
    }

    public function getLatestId($projectId, $isDraft = true)
    {
        /* @var Relation $this */
        $row = $this->select('id')
            ->where('project_id', $projectId)
            ->where('is_draft', $isDraft ? 't' : 'f')
            ->orderBy('revision', 'desc')
            ->first();

        return !is_null($row) ? $row->id : null;
    }
}
