<?php

namespace App\Models\Projects;

use App\Models\Investors\Investor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class ProjectInvestor extends Model
{
    use HasFactory;

    protected $table = "tr_project_investor";

    protected $fillable = [
        'project_id',
        'project_sk_id',
        'investor_id',
        'investment_value',
        'shares_value',
        'shares_percentage'
    ];

    public $defaultSelects = [
        'investment_value',
        'shares_value',
        'shares_percentage'
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
        $model = new ProjectInvestor();
        return $model->defaultWith(is_null($selects) ? $model->defaultSelects : $selects, $query);
    }

    /**
     * function untuk setting default with apa saja yang akan sering dipakai
     * tetapi jangan banyak-banyak karena akan memperngaruhi proses loading page
     *
     * @param Relation|ProjectInvestor $query
     * @param array $selects
     *
     * @return Relation
     * */
    private function _defaultWith($query, $selects = [])
    {
        return $query->with([
            'investor' => function($query) {
                Investor::foreignWith($query);
            }
        ])->select('tr_project_investor.id', 'investor_id')->addSelect($selects);
    }

    /**
     * function defaultWith yang digunakan untuk dipanggil public
     *
     * @param array $selects
     * @param Relation|ProjectInvestor|null $query
     *
     * @return Relation
     * */
    public function defaultWith($selects = [], $query = null)
    {
        return $this->_defaultWith(is_null($query) ? $this : $query, $selects);
    }

    public function investor()
    {
        return $this->hasOne(Investor::class, 'id', 'investor_id');
    }
}
