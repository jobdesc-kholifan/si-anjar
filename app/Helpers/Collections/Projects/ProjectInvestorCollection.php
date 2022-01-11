<?php

namespace App\Helpers\Collections\Projects;

use App\Helpers\Collections\Collection;
use App\Helpers\Collections\Investors\InvestorCollection;

class ProjectInvestorCollection extends Collection
{

    public function getId()
    {
        return $this->get('id');
    }

    public function getInvestmentValue()
    {
        return $this->get('investment_value');
    }

    public function getSharesValue()
    {
        return $this->get('shares_value');
    }

    public function getSharesPercentage()
    {
        return $this->get('shares_percentage');
    }

    public function getInvestorId()
    {
        return $this->get('investor_id');
    }

    /**
     * @return InvestorCollection
     * */
    public function getInvestor()
    {
        if($this->hasNotEmpty('investor'))
            return new InvestorCollection($this->get('investor'));

        return new InvestorCollection();
    }
}
