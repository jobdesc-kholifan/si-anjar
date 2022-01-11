<?php

namespace App\Helpers\Collections\Investors;

use Illuminate\Support\Collection;

class InvestorBankArray extends Collection
{

    public function __construct($items)
    {
        parent::__construct(collect($items)->map(function($data) {
            return new InvestorBankCollection($data);
        })->toArray());
    }

    /**
     * @return InvestorBankCollection
     * */
    public function first(callable $callback = null, $default = null)
    {
        $first = parent::first($callback, $default);

        return !is_null($first) ? $first : new InvestorBankCollection();
    }

    /**
     * @return InvestorBankCollection[]
     * */
    public function all()
    {
        return parent::all();
    }

    /**
     * @return InvestorBankCollection|null
     * */
    public function get($key, $default = null)
    {
        return parent::get($key, $default);
    }
}
