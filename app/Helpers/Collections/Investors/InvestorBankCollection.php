<?php

namespace App\Helpers\Collections\Investors;

use App\Helpers\Collections\Banks\BankCollection;
use App\Helpers\Collections\Collection;

class InvestorBankCollection extends Collection
{

    public function getId()
    {
        return $this->get('id');
    }

    public function getBankId()
    {
        return $this->get('bank_id');
    }

    /**
     * @return BankCollection
     * */
    public function getBank()
    {
        if($this->hasNotEmpty('bank'))
            return new BankCollection($this->get('bank'));

        return new BankCollection();
    }

    public function getBranchName()
    {
        return $this->get('branch_name');
    }

    public function getNoRekening()
    {
        return $this->get('no_rekening');
    }

    public function getOnBehalfOf()
    {
        return $this->get('atas_nama');
    }
}
