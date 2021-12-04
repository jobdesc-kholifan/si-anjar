@extends('skins.template')

@section('content')
    <x-content-header :title='$title' :breadcrumbs="$breadcrumbs"/>

    <section class="content">
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-header">
                    <h3 class="card-title">Data {{ $title }}</h3>
                    <div class="card-actions">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="actions.create()">
                            <i class="fa fa-plus"></i>
                            <span>Tambah</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="w-100">
                        <table class="table table-striped table-hover" id="table-data">
                            <thead>
                            <tr>
                                <th data-name="no" data-orderable="false" data-searchable="false">No</th>
                                <th data-data="name" data-name="name">Nama</th>
                                <th data-data="icon" data-name="icon">Icon</th>
                                <th data-data="slug" data-name="slug">Slug</th>
                                <th data-data="parent.name" data-name="parent_id">Parent</th>
                                <th data-data="action" data-name="action">Aksi</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script-footer')
    <script src="{{ asset('dist/js/actions-v2.js') }}"></script>
    <script type="text/javascript">
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
        actions.edit = function(id) {
            window.location.href = "{{ url()->current() }}/{id}".format({id: id});
        };

        actions.callback.form.onSuccessCallback = function(res) {
            window.location.href = "{{ url()->current() }}/{id}".format({id: res.data.id});
        };

        actions.build();
    </script>
@endpush
