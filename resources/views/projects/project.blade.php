<?php

$hasAccessCreate = findPermission(DBMenus::project)->hasAccess(DBFeature::view);

?>
@extends('skins.template')

@section('content')
    <x-content-header :title='$title' :breadcrumbs="$breadcrumbs"/>
    <section class="content">
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-header">
                    <h3 class="card-title">Data {{ $title }}</h3>
                    <div class="card-actions">
                        @if($hasAccessCreate)
                            <a href="{{ route(DBRoutes::projectCreate) }}" type="button" class="btn btn-outline-primary btn-sm">
                                <i class="fa fa-plus"></i>
                                <span>Tambah</span>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="w-100">
                        <table class="table table-striped table-hover" id="table-data">
                            <thead>
                            <tr>
                                <th data-data="checkbox" data-name="no" data-orderable="false" data-searchable="false"></th>
                                <th data-data="project_code" data-name="project_code">ID Proyek</th>
                                <th data-data="project_name" data-name="project_name">Nama Proyek</th>
                                <th data-data="project_value" data-name="project_value">Nilai Proyek</th>
                                <th data-data="finish_date" data-name="finish_date">Tgl Berakhir</th>
                                <th data-data="project_value" data-name="project_value">Dana Dibagikan</th>
                                <th data-data="status" data-name="project_value">Status</th>
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
        actions.build();
    </script>
@endpush
