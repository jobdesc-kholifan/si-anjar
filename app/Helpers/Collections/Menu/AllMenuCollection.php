<?php

namespace App\Helpers\Collections\Menu;

use Illuminate\Support\Collection;

class AllMenuCollection
{

    /* @var Collection */
    protected $menus;

    public function __construct($datas)
    {
        $this->menus = collect($datas)->map(function($data) {
            return new MenuCollection($data);
        });
    }

    /**
     * @return MenuCollection[]
     * */
    public function getParent($menuId = null)
    {
        return $this->menus->filter(function($data) use ($menuId) {
            /* @var MenuCollection $data */
            if($data->getParentId() == $menuId && count($data->privileges()) > 0) {
                $hasAccess = false;
                for($i = 0; $i <= count($data->privileges()) - 1; $i++) {
                    if($data->privileges()[$i]->getFeature()->getSlug() == \DBFeature::view) {
                        $hasAccess = true;
                        break;
                    }
                }

                if($hasAccess) return $data;
            }

            return null;
        })->sort(function($a, $b) {
            /**
             * @var MenuCollection $a
             * @var MenuCollection $b
             * */
            return $a->getSequence() > $b->getSequence();
        })->toArray();
    }

    public function hasChild($menuId)
    {
        return count($this->getParent($menuId)) > 0;
    }

    public function renderChild($menuId, $route)
    {
        $menus = new AllMenuCollection($this->menus);

        return view('skins.sidebar-menu', [
            'route' => $route,
            'menuId' => $menuId,
            'menus' => $menus,
        ]);
    }
}
