<?php

namespace App\Helpers\Collections\Config;

use App\Helpers\Collections\Collection;
use App\Models\Masters\Config;
use Illuminate\Database\Eloquent\Relations\Relation;

class ConfigCollection extends Collection
{
    static public function create($values) {
        /* @var Config|Relation $model */
        $model = new Config();
        return new ConfigCollection($model->create($values));
    }

    static public function find($id)
    {
        /* @var Config|Relation $model */
        $model = new Config();
        return new ConfigCollection($model->find($id));
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getName($default = '-')
    {
        return $this->get('name', $default);
    }

    public function getSlug()
    {
        return $this->get('slug');
    }

    public function getSequence()
    {
        return $this->get('sequence');
    }

    public function getDesc()
    {
        return $this->get('description');
    }

    /**
     * @return ConfigCollection()
     * */
    public function parent()
    {
        if(!is_null($this->has('parent')))
            return new ConfigCollection($this->get('parent'));

        return new ConfigCollection();
    }
}
