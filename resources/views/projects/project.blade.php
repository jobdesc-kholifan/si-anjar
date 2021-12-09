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
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="actions.create()">
                                <i class="fa fa-plus"></i>
                                <span>Tambah</span>
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="w-100">
                        <table class="table table-striped table-hover" id="table-data">
                            <thead>
                            <tr>
                                <th data-data="checkbox" data-name="no" data-orderable="false" data-searchable="false"></th>
                                <th data-data="no_ktp" data-name="no_ktp">ID Proyek</th>
                                <th data-data="investor_name" data-name="investor_name">Nama Proyek</th>
                                <th data-data="investor_name" data-name="investor_name">Nilai Proyek</th>
                                <th data-data="created_at" data-name="created_at">Tgl Berakhir</th>
                                <th data-data="phone_number" data-name="phone_number">Dana Dibagikan</th>
                                <th data-data="phone_number" data-name="phone_number">Status</th>
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
        actions.create = function() {
            window.location.href = "{{ route(DBRoutes::projectCreate) }}"
        };

        actions.build();
    </script>
@endpush