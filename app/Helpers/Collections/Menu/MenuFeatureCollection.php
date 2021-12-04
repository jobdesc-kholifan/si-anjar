<?php

namespace App\Helpers\Collections\Menu;

use App\Helpers\Collections\Collection;

class MenuFeatureCollection extends Collection
{

    public function getId()
    {
        return $this->get('id');
    }

    public function getTitle()
    {
        return $this->get('title');
    }

    public function getSlug()
    {
        return $this->get('slug');
    }

    public function getDesc()
    {
        return $this->get('description');
    }
}
