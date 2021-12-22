@extends('skins.template')

@section('content')
    <x-content-header :title='$title' :breadcrumbs="$breadcrumbs"/>
    <section class="content">
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-header">
                    <h3 class="card-title">Data {{ $title }}</h3>
                </div>
                <div class="card-body">
                    <div class="w-100">
                        <table class="table table-striped table-hover" id="table-data">
                            <thead>
                            <tr>
                                <th data-data="checkbox" data-name="no" data-orderable="false" data-searchable="false">No</th>
                                <th data-data="no_sk" data-name="no_sk">No. SK Terakhir</th>
                                <th data-data="project.project_name" data-searchable="false" data-orderable="false" >Nama Proyek</th>
                                <th data-data="printed_at" data-name="printed_at">Tgl Cetak Terakhir</th>
                                <th data-data="action" data-orderable="false" data-searchable="false" style="width: 200px">Aksi</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script-footer')
    <script src="{{ asset('dist/js/actions-v2.js') }}"></script>
    <script src="{{ asset('dist/js/investor-bank.js') }}"></script>
    <script src="{{ asset('dist/js/upload-v2.js') }}"></script>
    <script type="text/javascript">
        let actionsProjectSK;
        const actions = new Actions("{{ url()->current() }}");
        actions.datatable.params = {
            _token: "{{ csrf_token() }}",
        };
        actions.datatable.columnDefs = [
            {
                targets: 0,
                width: 20,
                render: (data, type, row, meta) => {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
            }
        ];

        actions.showProject = function(id) {
            $.createModal({
                url: '{{ url()->current() }}/show-project',
                data: {
                    id: id
                },
            }).open();
        };

        actions.showSK = function(id) {
            $.createModal({
                url: "{{ url()->current() }}/show-sk",
                data: {id: id},
                modalSize: 'modal-xl',
                onLoadComplete: (res) => {
                    actionsProjectSK = new Actions("{{ url()->current() }}/show-sk");
                    actionsProjectSK.selectors.table = "#table-project-sk";
                    actionsProjectSK.datatable.params = {
                        _token: "{{ csrf_token() }}",
                        id: id,
                    };
                    actionsProjectSK.datatable.columnDefs = [
                        {
                            targets: 0,
                            width: 20,
                            render: (data, type, row, meta) => {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            },
                        }
                    ];
                    actionsProjectSK.showDetailProject = function(id) {
                        window.location.href = "{{ route(DBRoutes::projectEdit, ['__id__']) }}?tab=proyek".route({id: id});
                    };
                    actionsProjectSK.build();
                }
            }).open();
        };
        actions.build();
    </script>
@endpush
