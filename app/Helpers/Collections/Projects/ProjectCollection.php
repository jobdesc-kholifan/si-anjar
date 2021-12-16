<?php

namespace App\Helpers\Collections\Projects;

use App\Helpers\Collections\Collection;
use App\Helpers\Collections\Config\ConfigCollection;
use App\Models\Projects\Project;

class ProjectCollection extends Collection
{

    /**
     * @param int $id
     * @return ProjectCollection
     *
     * @throws \Exception
     */
    static public function find($id)
    {
        $row = Project::query()->find($id);
        if(is_null($row))
            throw new \Exception(\DBMessages::corruptData, \DBCodes::authorizedError);

        return new ProjectCollection($row);
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getCode()
    {
        return $this->get('project_code');
    }

    public function getName()
    {
        return $this->get('project_name');
    }

    public function getCategoryId()
    {
        return $this->get('project_category_id');
    }

    /**
     * @return ConfigCollection
     * */
    public function getCategory()
    {
        if($this->hasNotEmpty('project_category'))
            return new ConfigCollection($this->get('project_category'));

        return new ConfigCollection();
    }

    public function getValue()
    {
        return $this->get('project_value');
    }

    public function getSharesValue()
    {
        return $this->get('project_shares', 0);
    }

    public function getModalValue()
    {
        return $this->get('modal_value', 0);
    }

    public function getStartDate($format = 'd/m/Y')
    {
        $date = $this->get('start_date');
        return !is_null($date) ? date($format, $date) : null;
    }

    public function getFinishDate($format = 'd/m/Y')
    {
        $date = $this->get('start_date');
        return !is_null($date) ? date($format, $date) : null;
    }

    public function getEstimateProfitValue()
    {
        return $this->get('estimate_profit_value');
    }

    public function getEstimateProfitId()
    {
        return $this->get('estimate_profit_id');
    }

    /**
     * @return ConfigCollection
     * */
    public function getEstimateProfit()
    {
        if($this->hasNotEmpty('estimate_profit'))
            return new ConfigCollection($this->get('estimate_profit'));

        return new ConfigCollection();
    }
}
