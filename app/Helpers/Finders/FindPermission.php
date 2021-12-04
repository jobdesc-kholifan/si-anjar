<?php

namespace App\Helpers\Finders;

use App\Helpers\Collections\Menu\MenuCollection;

class FindPermission
{

    /* @var MenuCollection */
    protected $menu;

    public function __construct($slugMenu)
    {
        if(!empty(session('menu')))
            $this->menu = new MenuCollection(
                collect(session('menu'))->filter(function($data) use ($slugMenu) {
                    if($data['slug'] == $slugMenu)
                        return $data;

                    return false;
                })->first()
            );
    }

    /**
     * @throws \Exception
     */
    public function hasAccess($slugFeature)
    {
        $hasAccess = false;
        if(!empty($this->menu))
            foreach($this->menu->privileges() as $permission) {
                if($permission->getFeature()->getSlug() == $slugFeature) {
                    $hasAccess = true;
                    break;
                }
            }

        return $hasAccess;
    }

    /**
     * @throws \Exception
     */
    public function hasAccessOrFail($slugFeature)
    {
        $hasAccess = $this->hasAccess($slugFeature);

        if(!$hasAccess)
            throw new \Exception(\DBMessages::permissionRequired, \DBCodes::permissionError);
    }
}
