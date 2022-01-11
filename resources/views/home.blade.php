@extends('skins.template')

@section('content')
    <x-content-header :title='$title' :breadcrumbs="$breadcrumbs"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-6 col-lg-2">
                    <div class="info-box shadow" data-toggle="box-dashboard" data-url="{{ route(DBRoutes::dashboardInvestor) }}">
                        <span class="info-box-icon bg-cyan"><i class="fa fa-user-tie"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text" data-name="value">0</span>
                            <span class="info-box-number">Investor</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-2">
                    <div class="info-box shadow" data-toggle="box-dashboard" data-url="{{ route(DBRoutes::dashboardProject) }}">
                        <span class="info-box-icon bg-dark"><i class="fa fa-building"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text" data-name="value">0</span>
                            <span class="info-box-number">Proyek</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-3">
                    <div class="info-box shadow" data-toggle="box-dashboard" data-url="{{ route(DBRoutes::dashboardSurkas) }}">
                        <span class="info-box-icon bg-fuchsia"><i class="fa fa-calendar-alt"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text" data-name="value"></span>
                            <span class="info-box-number">Surkas Dibagikan Bulan Ini</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script-footer')
    <script src="{{ asset('dist/js/box-dashboard.js') }}"></script>
    <script type="text/javascript">
        $('[data-toggle=box-dashboard]').each((i, item) => {
            const box = new BoxDashboard(item);
            box.init();
        })
    </script>
@endpush
