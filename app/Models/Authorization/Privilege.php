<?php

namespace App\Models\Authorization;

use App\Models\Masters\Config;
use App\Models\Menus\Menu;
use App\Models\Menus\MenuFeature;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

class Privilege extends Model
{
    use HasFactory;

    protected $table = "ms_privilege";

    protected $fillable = [
        'role_id',
        'menu_id',
        'menu_feature_id',
        'has_access',
    ];

    public $defaultSelects = [];

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
        $model = new Privilege();
        return $model->defaultWith(is_null($selects) ? $model->defaultSelects : $selects, $query);
    }

    public function lastId()
    {
        $dbname = env('DB_DATABASE');
        $tableName = $this->getTable();
        return DB::select("SELECT AUTO_INCREMENT as nextId FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$dbname' AND TABLE_NAME = '$tableName'")[0]->nextId;
    }

    /**
     * function untuk setting default with apa saja yang akan sering dipakai
     * tetapi jangan banyak-banyak karena akan memperngaruhi proses loading page
     *
     * @param Relation|Privilege $query
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
     * @param Relation|Privilege|null $query
     *
     * @return Relation
     * */
    public function defaultWith($selects = [], $query = null)
    {
        return $this->_defaultWith(is_null($query) ? $this : $query, $selects);
    }

    public function role()
    {
        return $this->hasOne(Config::class, 'id', 'role_id');
    }

    public function menu()
    {
        return $this->hasOne(Menu::class, 'id', 'menu_id');
    }

    public function menu_feature()
    {
        return $this->hasOne(MenuFeature::class, 'id', 'menu_feature_id');
    }
}
