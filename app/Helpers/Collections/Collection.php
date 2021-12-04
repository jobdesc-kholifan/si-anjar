<?php

namespace App\Helpers\Collections;

class Collection
{

    /**
     * Set data private
     *
     * @var \Illuminate\Support\Collection $data
     * */
    protected $data;

    public function __construct($data = null)
    {

        /**
         * Jika data dari parameter adalah model collection
         * maka data akan dicovert ke array setelah itu dicover ke object
         *
         * @var object $data
         * */
        if(is_object($data) && method_exists($data, 'toArray'))
            $this->data = collect($data->toArray());

        /**
         * Jika data dari parameter adalah tidak ada method toArray
         * maka langsung convert ke collection
         *
         * @var object|null|array $data
         * */
        else $this->data = collect($data);
    }

    public function get($key, $default = null)
    {
        return $this->data->get($key) != '' ? $this->data->get($key) : $default;
    }

    public function has($key)
    {
        return $this->data->has($key) && !is_null($this->data->get($key));
    }

    public function hasNotEmpty($key)
    {
        return $this->has($key) && !empty($this->data->get($key));
    }

    public function toData()
    {
        return $this->data;
    }

    public function toArray()
    {
        return $this->data->toArray();
    }
}
