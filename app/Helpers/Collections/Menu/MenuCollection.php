<?php

namespace App\Helpers\Collections\Menu;

use App\Helpers\Collections\Collection;

class MenuCollection extends Collection
{

    public function getId()
    {
        return $this->get('id');
    }

    public function getName()
    {
        return $this->get('name');
    }

    public function getIcon()
    {
        return $this->get('icon');
    }

    public function getSlug()
    {
        return $this->get('slug');
    }

    public function getSequence()
    {
        return $this->get('sequence');
    }

    public function getParentId()
    {
        return $this->get('parent_id');
    }

    /**
     * @return MenuCollection
     * */
    public function getParent()
    {
        if($this->hasNotEmpty('parent'))
            return new MenuCollection($this->get('parent'));

        return new MenuCollection();
    }

    /**
     * @return PrivilegeCollection[]
     * */
    public function privileges()
    {
        if($this->hasNotEmpty('privileges'))
            return collect($this->get('privileges'))->map(function($data) {
                return new PrivilegeCollection($data);
            })->toArray();

        return [];
    }
}
