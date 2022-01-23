<?php

namespace App\Models\Projects;

use App\Models\Masters\Config;
use App\Models\Masters\File;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

class ProjectSurkas extends Model
{
    use HasFactory;

    protected $table = "tr_project_surkas";

    protected $fillable = [
        'project_id',
        'surkas_no',
        'surkas_value',
        'surkas_date',
        'admin_fee',
        'description',
        'other_description',
        'status_id',
    ];

    public $defaultSelects = [
        'surkas_no',
        'surkas_value',
        'surkas_date',
        'admin_fee',
        'description',
        'other_description',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            /* @var Project $model */
            $file = $model->file_lampiran_surkas()->get();
            fileUnlink($file);
            $model->file_lampiran_surkas()->delete();

        });
    }

    public function getSurkasDateAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->setTimezone(env('APP_TIMEZONE'))
            ->format('d/m/Y');
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
        $model = new ProjectSurkas();
        return $model->defaultWith(is_null($selects) ? $model->defaultSelects : $selects, $query);
    }

    /**
     * function untuk setting default with apa saja yang akan sering dipakai
     * tetapi jangan banyak-banyak karena akan memperngaruhi proses loading page
     *
     * @param Relation|ProjectSurkas $query
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
        ])->select('tr_project_surkas.id', 'tr_project_surkas.status_id')->addSelect($selects);
    }

    /**
     * function defaultWith yang digunakan untuk dipanggil public
     *
     * @param array $selects
     * @param Relation|ProjectSurkas|null $query
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

    public function lastId()
    {
        $data = $this->select(DB::raw('MAX(id) as maxid'))
            ->first();

        return !is_null($data->maxid) ? $data->maxid : 1;
    }

    public function defaultQuery()
    {
        return $this->defaultWith($this->defaultSelects);
    }

    public function file_lampiran_surkas()
    {
        return $this->hasMany(File::class, 'ref_id', 'id')
            ->whereHas('ref_type', function ($query) {
                /* @var Relation $query */
                $query->where('slug', \DBTypes::fileSurkasAttachment);
            })
            ->orderBy('id');
    }

    public function totalSurkas($projectId = null)
    {
        /* @var Relation $this */
        $query = $this->select(DB::raw('SUM(surkas_value) as total'))
            ->whereHas('status', function($query) {
                /* @var Relation $query */
                $query->where('slug', \DBTypes::statusSurkasApproved);
            });

        if(!is_null($projectId))
            $query->where('project_id', $projectId);

        $row = $query->first();
        return !is_null($row->total) ? $row->total : 0;
    }
}
