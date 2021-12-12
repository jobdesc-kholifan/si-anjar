<ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
    <li class="nav-item">
        <a class="nav-link" href="{{ route(DBRoutes::projectEdit, [$projectId]) }}?tab=pic">PIC</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route(DBRoutes::projectEdit, [$projectId]) }}?tab=proyek">Proyek</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route(DBRoutes::projectInvestor, [$projectId]) }}">Investor</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route(DBRoutes::projectSK, [$projectId]) }}">SK</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route(DBRoutes::projectSurkas, [$projectId]) }}">Surkas</a>
    </li>
</ul>
