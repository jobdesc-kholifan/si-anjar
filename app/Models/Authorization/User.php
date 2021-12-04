<?php

namespace App\Models\Authorization;

use App\Models\Masters\Config;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = "ms_users";

    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'description',
        'role_id',
        'user_name',
        'user_password',
        'status_id',
    ];

    public $defaultSelects = [
        'full_name',
        'email',
        'phone_number',
        'description',
        'user_name',
        'user_password',
    ];

    protected $hidden = [
        'user_password',
        'created_at',
        'updated_at',
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
        $model = new User();
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
     * @param Relation|User $query
     * @param array $selects
     *
     * @return Relation
     * */
    private function _defaultWith($query, $selects = [])
    {
        return $query->with([
            'role' => function($query) {
                Config::foreignWith($query, ['name']);
            },
            'status' => function($query) {
                Config::foreignWith($query, ['name']);
            }
        ])->select('id', 'role_id', 'status_id')->addSelect($selects);
    }

    /**
     * function defaultWith yang digunakan untuk dipanggil public
     *
     * @param array $selects
     * @param Relation|User|null $query
     *
     * @return Relation
     * */
    public function defaultWith($selects = [], $query = null)
    {
        return $this->_defaultWith(is_null($query) ? $this : $query, $selects);
    }

    public function getAuthPassword()
    {
        return $this->user_password;
    }

    public function role()
    {
        return $this->hasOne(Config::class, 'id', 'role_id');
    }

    public function status()
    {
        return $this->hasOne(Config::class, 'id', 'status_id');
    }
}
