<?php

use App\Helpers\Collections\Projects\ProjectCollection;

/**
 * @var ProjectCollection $project
 * */

?>
@extends('skins.template')

@section('content')
    <x-content-header :title='$title' :breadcrumbs="$breadcrumbs"/>

    <section class="content">
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-body">
                    <form id="form-project" method="post">
                        {{ csrf_field() }}
                        @include('projects.project-tab-menu')
                        <div class="tab-content">
                            <div class="tab-pane active show fade" id="content-pic">
                                @include('projects.project-tab-investor')
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script-footer')
    <script src="{{ asset('dist/js/actions-v2.js') }}"></script>
    <script src="{{ asset('dist/js/upload.js') }}"></script>
    <script type="text/javascript">
        const projectValue = {{ $project->getValue() }};
        const actionsInvestor = new Actions("{{ route(DBRoutes::projectInvestor, [$projectId]) }}");
        actionsInvestor.selectors.table = '#table-project-investor';
        actionsInvestor.datatable.params = {
            _token: "{{ csrf_token() }}",
        };
        actionsInvestor.calculatePercentage = function(value) {
            const percentage = value/projectValue * 100;
            const
            return ;
        }
        actionsInvestor.build();
    </script>
@endpush
