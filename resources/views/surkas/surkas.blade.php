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
                        <table class="table table-striped table-hover w-100" id="table-data">
                            <thead>
                            <tr>
                                <th data-data="surkas_no" data-name="surkas_no">No Surkas</th>
                                <th data-data="project.project_name" data-name="project.project_name">Nama Proyek</th>
                                <th data-data="surkas_date" data-name="surkas_date">Tgl Transfer</th>
                                <th data-data="surkas_value" data-name="surkas_value" class="text-right">Jumlah</th>
                                <th data-data="action" data-orderable="false" data-searchable="false">Aksi</th>
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
        actions.showProject = function(id) {
            $.createModal({
                url: '{{ route(DBRoutes::projectDetail, ["__id__"]) }}/show-project'.route({id: id}),
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
        actions.detail = function(projectId, id) {
            $.createModal({
                url: '{{ route(DBRoutes::projectSurkas, ['__id__']) }}/detail'.format({id: projectId}),
                data: {id: id},
                onLoadComplete: function(res) {
                    $('#file-lampiran').upload({
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
        actions.build();
    </script>
@endpush
