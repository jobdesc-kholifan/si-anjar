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
    <script src="{{ asset('dist/js/upload-v2.js') }}"></script>
    <script type="text/javascript">
        const actions = new Actions("{{ url()->current() }}");
        actions.datatable.params = {
            _token: "{{ csrf_token() }}",
        };
        actions.datatable.order = [[0, 'asc']];

        actions.showProject = function(id) {
            $.createModal({
                url: '{{ url()->current() }}/show-project',
                data: {
                    id: id
                },
                onLoadComplete: function(res) {
                    $('#file-bukti-transfer').upload({
                        name: 'file_bukti_transfer',
                        allowed: ['image/*', 'application/pdf'],
                        readOnly: true,
                        getMimeType: (file) => file.mime_type,
                        getPreview: (file) => file.preview
                    });

                    $('#file-lampiran-project').upload({
                        name: 'file_lampiran[]',
                        allowed: ['image/*'],
                        multiple: true,
                        withDescription: true,
                        readOnly: true,
                        getMimeType: (file) => file.mime_type,
                        getPreview: (file) => file.preview,
                        getDesc: (file) => file.description
                    });
                }
            }).open();
        };
        actions.showSK = function(id) {
            $.createModal({
                url: '{{ url()->current() }}/show-sk',
                data: {
                    id: id
                },
            }).open();
        };
        actions.showInvestor = function(projectId, id) {
            $.createModal({
                url: '{{ route(DBRoutes::projectSK, ['__id__']) }}/detail'.route({id: projectId}),
                data: {
                    id: id
                },
                modalSize: 'modal-xl',
                onLoadComplete: function(res, modal) {
                    const actionsSKInvestor = new Actions("{{ route(DBRoutes::projectInvestor, ['__id__']) }}".route({id: projectId}));
                    actionsSKInvestor.selectors.table = '#table-project-investor';
                    actionsSKInvestor.datatable.params = {
                        _token: "{{ csrf_token() }}",
                        sk_id: id,
                    };
                    actionsSKInvestor.datatable.columnDefs = [
                        {
                            targets: 0,
                            width: 20,
                            render: (data, type, row, meta) => {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            },
                        },
                        {
                            targets: 5,
                            render: (data) => {
                                const $wrapper = $('<div>', {class: 'text-center'});
                                $wrapper.html(`${data} %`);

                                return $wrapper.get(0).outerHTML;
                            },
                        }
                    ];
                    actionsSKInvestor.build();
                },
            }).open();
        };
        actions.build();
    </script>
@endpush
