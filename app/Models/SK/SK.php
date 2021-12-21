<?php

namespace App\Models\SK;

use App\Models\Masters\File;
use App\Models\Projects\Project;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;


class SK extends Model
{
    use HasFactory;

    protected $table = "tr_project_sk";

    public $defaultSelects = [
        'revision',
        'no_sk',
        'printed_at',
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
        $model = new SK();
        return $model->defaultWith(is_null($selects) ? $model->defaultSelects : $selects, $query);
    }

    /**
     * function untuk setting default with apa saja yang akan sering dipakai
     * tetapi jangan banyak-banyak karena akan memperngaruhi proses loading page
     *
     * @param Relation|SK $query
     * @param array $selects
     *
     * @return Relation
     * */
    private function _defaultWith($query, $selects = [])
    {
        return $query->with([])->select('id')->addSelect($selects);
    }

    /**
     * function defaultWith yang digunakan untuk dipanggil public
     *
     * @param array $selects
     * @param Relation|SK|null $query
     *
     * @return Relation
     * */
    public function defaultWith($selects = [], $query = null)
    {
        return $this->_defaultWith(is_null($query) ? $this : $query, $selects);
    }

    public function defaultQuery()
    {
        return $this->defaultWith($this->defaultSelects)
            ->with([
                'project' => function ($query) {
                    Project::foreignWith($query);
                },
            ])
            ->addSelect('project_id');
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'id', 'project_id');
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
}
