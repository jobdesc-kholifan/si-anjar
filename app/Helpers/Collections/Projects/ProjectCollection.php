<?php

namespace App\Helpers\Collections\Projects;

use App\Helpers\Collections\Collection;
use App\Helpers\Collections\Config\ConfigCollection;
use App\Helpers\Collections\Files\FileArray;
use App\Helpers\Collections\Files\FileCollection;
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

    public function getValue($formatIDR = false)
    {
        $value = $this->get('project_value', 0);
        if($formatIDR)
            return IDR($value);

        return $value;
    }

    public function getSharesValue($formatIDR = false)
    {
        $value = $this->get('project_shares', 0);
        if($formatIDR)
            return IDR($value);

        return $value;
    }

    public function getModalValue()
    {
        return $this->get('modal_value', 0);
    }

    public function getStartDate($format = 'd/m/Y')
    {
        return !empty($this->get('start_date')) ? dbDate($this->get('start_date'), $format) : null;
    }

    public function getFinishDate($format = 'd/m/Y')
    {
        return !empty($this->get('finish_date')) ? dbDate($this->get('finish_date'), $format) : null;
    }

    public function getEstimateProfitValue()
    {
        $value = $this->get('estimate_profit_value');
        if($this->getEstimateProfit()->getSlug() == \DBTypes::projectValueNominal)
            return IDR($value);

        return IDR($value, '') . "%";
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

    public function getInvestors()
    {
        if($this->hasNotEmpty('data_investor'))
            return new ProjectInvestorArray($this->get('data_investor'));

        return new ProjectInvestorArray([]);
    }

    /**
     * @return FileCollection
     * */
    public function getFileProposal()
    {
        if($this->hasNotEmpty('file_proposal'))
            return new FileCollection($this->get('file_proposal'));

        return new FileCollection();
    }

    /**
     * @return FileCollection
     * */
    public function getFileEvidence()
    {
        if($this->hasNotEmpty('file_bukti_transfer'))
            return new FileCollection($this->get('file_bukti_transfer'));

        return new FileCollection();
    }

    /**
     * @return FileArray
     * */
    public function getFileAttachment()
    {
        if($this->hasNotEmpty('file_attachment'))
            return new FileArray($this->get('file_attachment'));

        return new FileArray([]);
    }
}
