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
                            @include('projects.project-tab-sk')
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
        const actionsSK = new Actions("{{ route(DBRoutes::projectSK, [$projectId]) }}");
        actionsSK.selectors.table = '#table-project-sk';
        actionsSK.datatable.params = {
            _token: "{{ csrf_token() }}",
        };
        actionsSK.functionVary = function() {
            alert("test");
        };
        actionsSK.build();
    </script>
@endpush
