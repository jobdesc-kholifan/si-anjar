<?php

namespace App\Helpers\Collections\Menu;

use App\Helpers\Collections\Collection;

class PrivilegeCollection extends Collection
{

    public function getId()
    {
        return $this->get('id');
    }

    public function hasAccess()
    {
        return $this->get('has_access');
    }

    /**
     * @return MenuFeatureCollection
     * */
    public function getFeature()
    {
        if($this->hasNotEmpty('menu_feature'))
            return new MenuFeatureCollection($this->get('menu_feature'));

        return new MenuFeatureCollection();
    }
}
