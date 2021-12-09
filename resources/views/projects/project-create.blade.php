@extends('skins.template')

@section('content')
    <x-content-header :title='$title' :breadcrumbs="$breadcrumbs"/>

    <section class="content">
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pic" data-toggle="pill" href="#content-pic" role="tab" aria-selected="true">PIC</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="project" data-toggle="pill" href="#content-project" role="tab" aria-selected="true">Proyek</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="investor" data-toggle="pill" href="#content-investor" role="tab" aria-selected="true">Investor</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="sk" data-toggle="pill" href="#content-sk" role="tab" aria-selected="true">SK</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="surkas" data-toggle="pill" href="#content-surkas" role="tab" aria-selected="true">Surkas</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active show fade" id="content-pic">
                            @include('projects.project-create-pic')
                        </div>
                        <div class="tab-pane fade" id="content-project">
                            @include('projects.project-create-project')
                        </div>
                        <div class="tab-pane fade" id="content-investor">
                            @include('projects.project-create-investor')
                        </div>
                        <div class="tab-pane fade" id="content-sk">
                            @include('projects.project-create-sk')
                        </div>
                        <div class="tab-pane fade" id="content-surkas">
                            @include('projects.project-create-surkas')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script-footer')
    <script src="{{ asset('dist/js/actions-v2.js') }}"></script>
    <script src="{{ asset('dist/js/project-pic.js') }}"></script>
    <script src="{{ asset('dist/js/upload.js') }}"></script>
    <script type="text/javascript">
        FormComponents.daterangepicker.init();
        FormComponents.select2.init();

        const formPIC = new FormPIC('#form-pic');
        formPIC.add();

        const fileProposal = $('#file-proposal');
        fileProposal.upload({
            name: 'file_proposal',
            allowed: ['image/*'],
            showFileName: true,
        });

        const fileBuktiTransfer = $('#file-bukti-transfer');
        fileBuktiTransfer.upload({
            name: 'file_proposal',
            allowed: ['image/*'],
            showFileName: true,
        });

        const actionsInvestor = new Actions("{{ route(DBRoutes::projectInvestor) }}");
        actionsInvestor.build();

        const actionsSK = new Actions("{{ route(DBRoutes::projectSK) }}");
        actionsSK.build();

        const actionsSurkas = new Actions("{{ route(DBRoutes::projectSurkas) }}");
        actionsSurkas.build();
    </script>
@endpush
