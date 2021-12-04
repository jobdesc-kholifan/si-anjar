<?php

/**
 * @var \App\Helpers\Collections\Menu\AllMenuCollection $menus
 * @var string $menuId
 * @var array $route
 * */

?>
<ul class="nav nav-treeview">
    @foreach($menus->getParent($menuId) as $menu)
        <li class="nav-item{{ isParentMenuOpen($menu->getSlug(), $route) }}">
            <a href="{{ url($menu->getSlug()) }}" class="nav-link{{ isMenuActive($route, $menu->getSlug()) }}">
                <i class="far fa-circle nav-icon"></i>
                <p>
                    {{ $menu->getName() }}
                    @if($menus->hasChild($menu->getId()))
                        <i class="right fas fa-angle-right"></i>
                    @endif
                </p>
            </a>
            @if($menus->hasChild($menu->getId()))
                {!! $menus->renderChild($menu->getId(), $route) !!}
            @endif
        </li>
    @endforeach
</ul>
