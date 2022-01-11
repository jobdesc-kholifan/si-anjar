<?php

namespace App\Helpers\Collections\Banks;

use App\Helpers\Collections\Collection;
use App\Models\Masters\Bank;
use Illuminate\Database\Eloquent\Relations\Relation;

class BankCollection extends Collection
{

    /**
     * @param array $values
     * @return BankCollection
     * */
    static public function create($values) {
        /* @var Bank|Relation $bank */
        $bank = new Bank();
        return new BankCollection($bank->create($values));
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getBankCode()
    {
        return $this->get('bank_code');
    }

    public function getBankName()
    {
        return $this->get('bank_name');
    }

    public function getDesc()
    {
        return $this->get('description');
    }
}
