<?php

namespace App\Models\Projects;

use App\Helpers\Collections\Projects\ProjectSKCollection;
use App\Models\Masters\Config;
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
        'is_draft',
        'status_id',
        'pdf_payload'
    ];

    public $defaultSelects = [
        'revision',
        'no_sk',
        'printed_at',
        'is_draft',
        'pdf_payload'
    ];

    public function getPrintedAtAttribute($data)
    {
        return !empty($data) ? dbDate($data, 'd/m/Y H:i:s') : null;
    }

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
            'status' => function($query) {
                Config::foreignWith($query);
            }
        ])->select('id', 'status_id')->addSelect($selects);
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

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function status()
    {
        return $this->hasOne(Config::class, 'id', 'status_id');
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

    /**
     * @param int $projectId
     * @return ProjectSKCollection|null
     * */
    public function getLatestReleased($projectId)
    {
        /* @var Relation|ProjectSK $this */
        $row = $this->defaultWith($this->defaultSelects)
            ->where('project_id', $projectId)
            ->where('is_draft',  'f')
            ->whereHas('status', function($query) {
                /* @var Relation $query */
                $query->where('slug', \DBTypes::statusSKApproved);
            })
            ->orderBy('revision', 'desc')
            ->first();

        if(is_null($row))
            return null;

        return new ProjectSKCollection($row);
    }

    public function getLatestReleasedId($projectId)
    {
        $sk = $this->getLatestReleased($projectId);

        return !is_null($sk) ? $sk->getId() : null;
    }

    public function withProject()
    {
        /* @var Relation $this */
        return $this->select('project_id', DB::raw('MAX(no_sk) as no_sk'), DB::raw('MAX(id) as id'), DB::raw('(SELECT sub.printed_at FROM tr_project_sk sub WHERE sub.id = max(tr_project_sk.id)) as printed_at'))
            ->with([
                'project' => function($query) {
                    Project::foreignWith($query);
                }
            ])
            ->whereHas('status', function($query) {
                /* @var Relation $query */
                $query->where('slug', \DBTypes::statusSKApproved);
            })
            ->addSelect('project_id')
            ->groupBy('project_id');
    }
}
