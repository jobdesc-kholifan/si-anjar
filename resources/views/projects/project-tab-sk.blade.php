<?php

$hasUpdate = findPermission(DBMenus::project)->hasAccess(DBFeature::update);

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
                                <h4 class="pt-3 pb-2 px-2 mb-4 border-bottom">Form SK</h4>
                                <div class="form-group text-right">
                                    @if($countInvestor > 0 && $hasUpdate)
                                    <a href="{{ route(DBRoutes::projectSKUpdate, [$projectId]) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fa fa-plus-circle"></i>
                                        <span class="ml-2">Pembaruan SK</span>
                                    </a>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <div class="w-100">
                                        <table class="table table-striped table-hover w-100" id="table-project-sk">
                                            <thead>
                                            <tr>
                                                <th data-name="no" data-orderable="false" data-searchable="false">No</th>
                                                <th data-data="revision" data-name="revision">Pembaruan Ke</th>
                                                <th data-data="no_sk" data-name="no_sk">No SK</th>
                                                <th data-data="printed_at" data-name="printed_at">Tanggal Cetak</th>
                                                <th data-data="action" data-orderable="false" data-searchable="false" style="width: 200px">Aksi</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                                <div class="footer-actions">
                                    <a href="{{ route(DBRoutes::projectSK, [$projectId]) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa fa-angle-left"></i>
                                        <span class="ml-2">Sebelumnya</span>
                                    </a>
                                    <a href="{{ route(DBRoutes::projectSurkas, [$projectId]) }}" class="btn btn-outline-primary btn-sm">
                                        <span class="mr-2">Selanjutnya</span>
                                        <i class="fa fa-angle-right"></i>
                                    </a>
                                </div>
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
        let actionsSKInvestor;

        const projectValue = {{ $project->getValue() }};
        const actionsSK = new Actions("{{ route(DBRoutes::projectSK, [$projectId]) }}");

        actionsSK.selectors.table = '#table-project-sk';
        actionsSK.datatable.params = {
            _token: "{{ csrf_token() }}",
        };
        actionsSK.datatable.columnDefs = [
            {
                targets: 0,
                width: 20,
                render: (data, type, row, meta) => {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
            }
        ];
        actionsSK.detail = function(id) {
            $.createModal({
                url: '{{ url()->current() }}/detail',
                data: {
                    id: id
                },
                modalSize: 'modal-xl',
                onLoadComplete: function(res, modal) {
                    actionsSKInvestor = new Actions("{{ route(DBRoutes::projectInvestor, [$projectId]) }}");
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
        actionsSK.build();
    </script>
@endpush
