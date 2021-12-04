<?php

namespace App\Models\Menus;

use App\Models\Authorization\Privilege;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    use HasFactory;

    protected $table = "ms_menus";

    protected $fillable = [
        'parent_id',
        'name',
        'icon',
        'slug',
        'sequence',
    ];

    public $defaultSelects = [
        'name',
        'icon',
        'slug',
        'sequence',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($menu) {
            /* @var Menu $menu */
            $menu->features()->delete();
        });
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
        $model = new Menu();
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
     * @param Relation|Menu $query
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
     * @param Relation|Menu|null $query
     *
     * @return Relation
     * */
    public function defaultWith($selects = [], $query = null)
    {
        return $this->_defaultWith(is_null($query) ? $this : $query, $selects);
    }

    public function parent()
    {
        return $this->hasOne(Menu::class, 'id', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id', 'id');
    }

    public function features()
    {
        return $this->hasMany(MenuFeature::class, 'menu_id', 'id');
    }

    public function privileges()
    {
        return $this->hasMany(Privilege::class, 'menu_id', 'id');
    }
}
