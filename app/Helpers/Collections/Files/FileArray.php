<?php

namespace App\Helpers\Collections\Files;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File as SupportFile;

class FileArray extends Collection
{

    public function __construct($item)
    {
        parent::__construct(collect($item)->map(function($data) {
            return new FileCollection($data);
        })->toArray());
    }

    /**
     * @return FileCollection[]
     * */
    public function all()
    {
        return parent::all();
    }

    /**
     * @return FileCollection
     * */
    public function first(callable $callback = null, $default = null)
    {
        $first = parent::first($callback, $default);
        if(is_null($first))
            return new FileCollection();

        return $first;
    }

    public function toJson($options = 0)
    {
        $json = collect($this->all())->map(function($data) {
            /* @var FileCollection $data */
            return $data->toData()->toArray();
        });

        return json_encode($json->toArray(), $options);
    }
}
