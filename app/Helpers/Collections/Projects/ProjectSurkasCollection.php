<?php

namespace App\Helpers\Collections\Projects;

use App\Helpers\Collections\Collection;
use App\Helpers\Collections\Files\FileArray;

class ProjectSurkasCollection extends Collection
{

    public function getId()
    {
        return $this->get('id');
    }

    public function getNoSurkas()
    {
        return $this->get('surkas_no');
    }

    public function getSurkasValue()
    {
        return $this->get('surkas_value');
    }

    public function getAdminFee()
    {
        return $this->get('admin_fee');
    }

    public function getSurkasDate($format = 'd/m/Y')
    {
        $date = $this->get('surkas_date');
        return $date != null ? date($format, strtotime($date)) : null;
    }

    public function getDesc()
    {
        return $this->get('description');
    }

    public function getOtherDesc()
    {
        return $this->get('other_description');
    }

    /**
     * @return FileArray
     * */
    public function getFileAttachment()
    {
        if($this->hasNotEmpty('file_lampiran_surkas'))
            return new FileArray($this->get('file_lampiran_surkas'));

        return new FileArray([]);
    }
}
