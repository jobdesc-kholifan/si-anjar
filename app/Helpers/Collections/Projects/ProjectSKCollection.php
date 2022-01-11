<?php

namespace App\Helpers\Collections\Projects;

use App\Helpers\Collections\Collection;
use App\Helpers\Collections\Config\ConfigCollection;

class ProjectSKCollection extends Collection
{

    public function getId()
    {
        return $this->get('id');
    }

    public function getRevision()
    {
        return $this->get('revision');
    }

    public function getNoSK()
    {
        return $this->get('no_sk');
    }

    public function getPrintedAt($format = 'd/m/Y')
    {
        $date = $this->get('printed_at');
        return !is_null($date) ? date($format, strtotime($date)) : null;
    }

    public function isDraft()
    {
        return $this->get('is_draft');
    }

    public function getStatusId()
    {
        return $this->get('status_id');
    }

    /**
     * @return ConfigCollection
     * */
    public function getStatus()
    {
        if($this->hasNotEmpty('status'))
            return new ConfigCollection($this->get('status'));

        return new ConfigCollection();
    }

    public function getPdfPayload()
    {
        if($this->hasNotEmpty('pdf_payload'))
            return new PayloadDocumentSK($this->get('pdf_payload'));

        return new PayloadDocumentSK();
    }
}
