<?php

$active = !empty($tabActive) ? $tabActive : '';

?>
<ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
    <li class="nav-item">
        <a class="nav-link{{ $active == 'pic' ? ' active' : '' }}" href="{{ route(DBRoutes::projectEdit, [$projectId]) }}?tab=pic">PIC</a>
    </li>
    <li class="nav-item">
        <a class="nav-link{{ $active == 'proyek' ? ' active' : '' }}" href="{{ route(DBRoutes::projectEdit, [$projectId]) }}?tab=proyek">Proyek</a>
    </li>
    <li class="nav-item">
        <a class="nav-link{{ $active == 'investor' ? ' active' : '' }}" href="{{ route(DBRoutes::projectInvestor, [$projectId]) }}">Investor</a>
    </li>
    <li class="nav-item">
        <a class="nav-link{{ $active == 'sk' ? ' active' : '' }}" href="{{ route(DBRoutes::projectSK, [$projectId]) }}">SK</a>
    </li>
    <li class="nav-item">
        <a class="nav-link{{ $active == 'surkas' ? ' active' : '' }}" href="{{ route(DBRoutes::projectSurkas, [$projectId]) }}">Surkas</a>
    </li>
</ul>
