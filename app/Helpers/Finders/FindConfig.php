<?php

namespace App\Helpers\Finders;

use App\Helpers\Collections\Config\ConfigArrayCollection;
use App\Helpers\Contract\FinderContract;
use App\Models\Masters\Config;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

class FindConfig implements FinderContract
{

    static private $instance;

    /**
     * Inisialisai finder class dengan static method
     *
     * @param string|null|array $keyOrValues
     * @param array|string $values
     * @return ConfigArrayCollection
     * @throws \Exception
     * */
    static public function find($keyOrValues, $values = null) {
        if(is_null(self::$instance))
            self::$instance = new FindConfig();

        if(!is_null($values)) {
            self::$instance->setKey($keyOrValues);
            return self::$instance->in($values);
        }

        return self::$instance->in($keyOrValues);
    }

    /**
     * Inisialisai finder class dengan static m     ethod
     * dengan default key kolom type_code
     *
     * @param string|null $key
     * @return FindConfig
     * @throws \Exception
     * */
    static public function key($key = null) {
        if(is_null(self::$instance))
            self::$instance = new FindConfig();

        self::$instance->setKey($key);

        return self::$instance;
    }

    /**
     * Inisialisai finder class dengan static m     ethod
     * dengan default key kolom type_code
     *
     * @return ConfigArrayCollection
     * @throws \Exception
     * */
    static public function code($codes) {
        if(is_null(self::$instance))
            self::$instance = new FindConfig();

        return self::$instance->in($codes);
    }

    /**
     * Model dari table mstype
     *
     * @var Config|Relation
     * */
    protected $config;

    /**
     * Data field apa saja yang ada di tabel dan bisa digunakan
     * sebagai key untuk pencarian data
     *
     * @var array
     * */
    protected $fillable = [];

    /**
     * $key adalah nama field yang akan dijadikan acuan dalam
     * pengambilan data
     *
     * @var string
     * */
    protected $key = 'slug';

    public function __construct()
    {
        $this->config = new Config();

        $this->setFillable();
    }

    /**
     * Set data fillable ambil dari model type fillable ditambah
     * dengan primary key tabel
     *
     * @param array $array
     * */
    public function setFillable($array = [])
    {
        $this->fillable = collect($this->config->getFillable())
            ->add($this->config->getKeyName())
            ->merge($array)
            ->toArray();
    }

    /**
     * Menentukan field apa yang akan dijadikan acuan ketika
     * pengambilan data
     *
     * @throws \Exception
     * @param string $key
     * */
    public function setKey($key)
    {
        if(!is_null($key)) {
            if(!in_array($key, $this->fillable))
                throw new \Exception("Kolom {$key} tidak ditemukan di tabel {$this->config->getTable()}");

            $this->key = $key;
        }
    }

    public function parentIn($code)
    {
        $codes = is_array($code) ? $code : func_get_args();

        $types = $this->config->defaultWith($this->config->defaultSelects)
            ->with([
                'parent' => function($query) {
                    Config::foreignWith($query);
                }
            ])
            ->whereHas('parent', function($query) use ($codes) {
                /* @var Relation $query */
                $query->whereIn($this->key, $codes);
            })
            ->addSelect('parent_id')
            ->get();

        return new ConfigArrayCollection($this->key, $codes, $types->toArray());
    }

    /**
     * Mencari data tipe berdasarkan berdasarkan key value
     *
     * @param array|mixed $code
     * @return ConfigArrayCollection()
     * */
    public function in($code)
    {
        $codes = is_array($code) ? $code : func_get_args();

        $types = $this->config->defaultWith($this->config->defaultSelects)
            ->whereIn($this->key, $codes)
            ->get();

        return new ConfigArrayCollection($this->key, $codes, $types->toArray());
    }

    public function name($name)
    {
        $names = is_array($name) ? $name : func_get_args();

        $types = $this->config->defaultWith($this->config->defaultSelects)
            ->whereIn(DB::raw("TRIM(LOWER(name))"), $names)
            ->get();

        return new ConfigArrayCollection('name', $names, $types->toArray());
    }
}
