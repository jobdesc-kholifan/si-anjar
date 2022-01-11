<?php

namespace App\Helpers\Collections\Projects;

use Illuminate\Support\Collection;

class ProjectInvestorArray extends Collection
{

    public function __construct($data)
    {
        parent::__construct(collect($data)->map(function($data) {
            return new ProjectInvestorCollection($data);
        })->toArray());
    }

    /**
     * @return ProjectInvestorCollection
     * */
    public function first(callable $callback = null, $default = null)
    {
        $first = parent::first($callback, $default);
        return !is_null($first) ? $first : new ProjectInvestorCollection();
    }

    /**
     * @return ProjectInvestorCollection[]
     * */
    public function all()
    {
        return parent::all();
    }

    /**
     * @return ProjectInvestorCollection|null
     * */
    public function get($key, $default = null)
    {
        return parent::get($key, $default);
    }
}
