<?php

namespace App\Helpers\Collections\Config;

use App\Models\Masters\Config;
use Illuminate\Support\Collection;

class ConfigArrayCollection
{

    /**
     * Model dari table mstype
     *
     * @var Config
     * */
    protected $model;

    /**
     * Data collection array type
     *
     * @var ConfigCollection[]
     * */
    protected $datas;

    /**
     * Data array key values dari finder
     *
     * @var Collection
     * */
    protected $keys;

    /**
     * Kolom yang bisa digunakan untuk mapping
     * diambil dari fillable model
     *
     * @var Collection
     * */
    protected $fillableKeys = array();

    /**
     * Curren key yang digunakan untuk mapping data
     *
     * @var string
     * */
    protected $key = 'code';

    public function __construct($key, $keys, $datas)
    {
        $this->key = $key;
        $this->keys = collect($keys);
        $this->datas = collect($datas)->map(function($data) {
            return new ConfigCollection($data);
        });

        $this->model = new Config();
        $this->fillableKeys = collect($this->model->getFillable())
            ->add($this->model->getKeyName());
    }

    /**
     * Setting key apa yang akan digunakan untuk mapping data
     * Jika parameter $key kosong maka akan menggunkan default key
     *
     * @throws \Exception
     * @var string $key
     * */
    public function setKey($key = null)
    {
        if(!is_null($key)) {
            if(!in_array($this->key, $this->fillableKeys->toArray()))
                throw new \Exception("Tidak dapat mapping data, kolom {$this->key} tidak ditemukan di tabel {$this->model->getTable()}");

            $this->key = $key;
        }
    }

    /**
     * Ambil semua data yang telah didapatkan
     *
     * @return ConfigCollection[]
     * */
    public function all()
    {
        return $this->datas;
    }

    /**
     * Ambil data berdasarkan current key
     * Ketika terdapat data lebih dari 1 maka yang diambil hanya data pertama
     *
     * Jika $keyValue is null maka akan diambil data pertama dari $keys
     *
     * @throws \Exception
     * @var string|null $keyValue
     * @return ConfigCollection
     * */
    public function get($keyValue = null, $callback = null)
    {
        if (!is_null($callback) && $this->datas->count() == 0)
            return call_user_func_array($callback, [$keyValue]);

        else if(is_null($callback) && $this->datas->count() == 0)
            throw new \Exception("Finder tidak menemukan data {$keyValue}");

        if(is_null($keyValue))
            $keyValue = $this->keys->first();

        $data = $this->datas->filter(function($data) use ($keyValue) {
            /* @var ConfigCollection $data*/
            return $data->get($this->key) == $keyValue;
        });

        if (!is_null($callback) && $data->count() == 0)
            return call_user_func_array($callback, [$keyValue]);

        else if(is_null($callback) && $data->count() == 0)
            throw new \Exception("Data tipe {$keyValue} tidak ditemukan");

        return $data->first();
    }

    /**
     * Ambil data berdasarkan current key
     *
     * @throws \Exception
     * @var string|null $keyValue
     * @return ConfigCollection[]
     * */
    public function getArray($keyValue = null)
    {
        if(is_null($keyValue))
            $keyValue = $this->keys->first();

        $data = $this->datas->filter(function($data) use ($keyValue) {
            /* @var ConfigCollection $data*/
            return $data->get($this->key) == $keyValue;
        });

        return $data->toArray();
    }

    /**
     * Ambil data child dari tipe yang dicari
     *
     * @param string|null $keyValue
     * @return ConfigCollection[]
     * @throws \Exception
     * */
    public function children($keyValue = null)
    {
        if($this->datas->count() == 0)
            throw new \Exception("Data tipe tidak ditemukan");

        if(is_null($keyValue))
            $keyValue = $this->keys->first();

        $data = $this->datas->filter(function($data) use ($keyValue) {
            /* @var ConfigCollection $data*/
            return $data->parent()->get($this->key) == $keyValue;
        });

        return $data->toArray();
    }
}
