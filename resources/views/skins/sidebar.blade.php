<?php

use App\Helpers\Collections\Menu\AllMenuCollection;
use App\Helpers\Collections\Users\UserCollection;
use App\Models\Authorization\Privilege;
use App\Models\Menus\Menu;
use App\Models\Menus\MenuFeature;
use Illuminate\Database\Eloquent\Relations\Relation;

$user = UserCollection::session();

session()->put('menu', Menu::select(['id', 'parent_id', 'name', 'icon', 'slug', 'sequence'])
    ->with([
        'privileges' => function($query) use ($user) {
            Privilege::foreignWith($query)
                ->with([
                    'menu_feature' => function($query) {
                        MenuFeature::foreignWith($query);
                    }
                ])
                ->addSelect('role_id', 'has_access', 'menu_feature_id', 'menu_id')
                ->where('role_id', $user->getRoleId())
                ->where('has_access', true);
        }
    ])
    ->whereHas('privileges', function($query) use ($user) {
        /* @var Relation $query */
        $query->where('role_id', $user->getRoleId());
    })
    ->get()
    ->toArray()
);
$menus = new AllMenuCollection(session('menu'));

?>
<aside class="main-sidebar bg-gray-dark sidebar-dark-primary elevation-4">
    <a href="{{ url('/') }}" class="brand-link text-center py-2">
        <span class="brand-text font-weight-light" style="font-size: 30px">SI <b></b></span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-2 pb-3 mb-2 d-flex flex-column align-items-center">
            <div class="img-circle-medium img-contain bg-white mb-3" style="background-image: url('{{ asset('dist/img/noimage.png') }}')"></div>
            <div class="user-info">
                <a href="#" class="info-name d-block">{{ $user->getFullName() }}</a>
                <a href="#" class="info-level d-block">{{ $user->getRole()->getName() }}</a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @foreach($menus->getParent() as $menu)
                    <li class="nav-item{{ isParentMenuOpen($menu->getSlug(), $route) }}">
                        <a href="{{ url($menu->getSlug()) }}" class="nav-link{{ isMenuActive($route, $menu->getSlug()) }}">
                            {!! $menu->getIcon() !!}
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
        </nav>
    </div>
</aside>
