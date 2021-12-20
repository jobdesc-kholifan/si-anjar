<?php

namespace App\Models\Projects;

use App\Models\Masters\Config;
use App\Models\Masters\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    use HasFactory;

    protected $table = "tr_project";

    protected $fillable = [
        'project_code',
        'project_name',
        'project_category_id',
        'project_value',
        'project_shares',
        'start_date',
        'finish_date',
        'estimate_profit_value',
        'estimate_profit_id',
        'modal_value',
    ];

    public $defaultSelects = [
        'project_code',
        'project_name',
        'project_value',
        'project_shares',
        'modal_value',
        'start_date',
        'finish_date',
        'estimate_profit_value',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($model) {
            /* @var Project $model */
            $file = $model->file_proposal()->get();
            fileUnlink($file);
            $model->file_proposal()->delete();

            $file = $model->file_bukti_transfer()->get();
            fileUnlink($file);
            $model->file_bukti_transfer()->delete();

            $model->data_pic()->delete();
            $model->data_investor()->delete();
            $model->data_sk()->delete();
            $model->data_surkas()->delete();
        });
    }

    public function getStartDateAttribute($value)
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
        $model = new Project();
        return $model->defaultWith(is_null($selects) ? $model->defaultSelects : $selects, $query);
    }

    /**
     * function untuk setting default with apa saja yang akan sering dipakai
     * tetapi jangan banyak-banyak karena akan memperngaruhi proses loading page
     *
     * @param Relation|Project $query
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
     * @param Relation|Project|null $query
     *
     * @return Relation
     * */
    public function defaultWith($selects = [], $query = null)
    {
        return $this->_defaultWith(is_null($query) ? $this : $query, $selects);
    }
    public function lastId()
    {
        /* @var Relation $this */
        $data = $this->select(DB::raw('MAX(id) as maxId'))
            ->first();

        return !is_null($data->maxId) ? $data->maxId : 1;
    }


    public function data_pic()
    {
        return $this->hasMany(ProjectPIC::class, 'project_id', 'id');
    }

    public function data_investor()
    {
        return $this->hasMany(ProjectInvestor::class, 'project_id', 'id');
    }

    public function data_sk()
    {
        return $this->hasMany(ProjectSK::class, 'project_id', 'id');
    }

    public function data_surkas()
    {
        return $this->hasMany(ProjectSurkas::class, 'project_id', 'id');
    }

    public function project_category()
    {
        return $this->hasOne(Config::class, 'id', 'project_category_id');
    }

    public function estimate_profit()
    {
        return $this->hasOne(Config::class, 'id', 'estimate_profit_id');
    }

    public function file_proposal()
    {
        return $this->hasOne(File::class, 'ref_id', 'id')
            ->whereHas('ref_type', function($query) {
                /* @var Relation $query */
                $query->where('slug', \DBTypes::fileProjectProposal);
            });
    }

    public function file_bukti_transfer()
    {
        return $this->hasOne(File::class, 'ref_id', 'id')
            ->whereHas('ref_type', function($query) {
                /* @var Relation $query */
                $query->where('slug', \DBTypes::fileProjectBuktiTransfer);
            });
    }

    public function file_attachment()
    {
        return $this->hasMany(File::class, 'ref_id', 'id')
            ->whereHas('ref_type', function($query) {
                 /* @var Relation $query */
                $query->where('slug', \DBTypes::fileProjectAttachment);
            })
            ->orderBy('id');
    }

    public function defaultQuery()
    {
        return $this->defaultWith($this->defaultSelects)
            ->with([
                'data_pic' => function($query) {
                    ProjectPIC::foreignWith($query)
                        ->addSelect('project_id');
                },
                'project_category' => function($query) {
                    Config::foreignWith($query);
                },
                'estimate_profit' => function($query) {
                    Config::foreignWith($query);
                },
            ])
            ->addSelect('project_category_id', 'estimate_profit_id');
    }

    public function updateModal($projectId)
    {
        /* @var Relation $this */
        return $this->where('id', $projectId)
            ->update([
                'modal_value' => DB::raw("(
                    SELECT SUM(tr_project_investor.investment_value)
                    FROM tr_project_investor
                    WHERE tr_project_investor.project_id = $projectId
                    AND project_sk_id = (
                        SELECT tr_project_sk.id
                        FROM tr_project_sk
                        WHERE tr_project_sk.project_id = $projectId
                        ORDER BY tr_project_sk.revision DESC
                        LIMIT 1
                    )
                )")
            ]);
    }
}
