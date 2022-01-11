<?php

/**
 * @var ProjectCollection $project
 * */

use App\Helpers\Collections\Projects\ProjectCollection;

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
                                <h4 class="pt-3 pb-2 px-2 mb-4 border-bottom">Form Investor
                                    @if(!empty($noSK))
                                        - {{ $noSK }}
                                    @endif</h4>
                                <div class="form-group">
                                    <dl class="row">
                                        <dt class="col-4 col-sm-2">Proyek</dt>
                                        <dd class="col-8 col-sm-10">{{ $project->getName() }}</dd>
                                        <dt class="col-4 col-sm-2">Nilai Proyek</dt>
                                        <dd class="col-8 col-sm-10">{{ IDR($project->getValue()) }}</dd>
                                        <dt class="col-4 col-sm-2">Harga Per Lembar Saham</dt>
                                        <dd class="col-8 col-sm-10">{{ IDR($project->getSharesValue()) }} Lembar</dd>
                                        <dt class="col-4 col-sm-2">Modal Disetor</dt>
                                        <dd class="col-8 col-sm-10" id="label-modal-value">{{ IDR($project->getModalValue()) }}</dd>
                                        <dt class="col-4 col-sm-2">Kekurangan Modal Disetor</dt>
                                        <dd class="col-8 col-sm-10" id="label-modal-lack">{{ IDR($project->getValue() - $project->getModalValue()) }}</dd>
                                    </dl>
                                </div>
                                <div class="form-group">
                                    <div class="w-100">
                                        <table class="table table-striped table-hover w-100" id="table-project-investor">
                                            <thead>
                                            <tr>
                                                <th data-name="no" data-orderable="false" data-searchable="false">No</th>
                                                <th data-data="investor.no_ktp" data-name="investor.no_ktp">No. KTP</th>
                                                <th data-data="investor.investor_name" data-name="investor.investor_name">Nama Investor</th>
                                                <th data-data="investment_value" data-name="shares_value" class="text-right text-bold">Nominal</th>
                                                <th data-data="shares_value" data-name="investment_value" class="text-center">Jumlah Lembar Saham</th>
                                                <th data-data="shares_percentage" data-name="shares_percentage" class="text-center">Porsi Saham</th>
                                            </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="footer-actions">
                                    <a href="{{ route(DBRoutes::projectEdit, [$projectId]) }}?tab=proyek" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa fa-angle-left"></i>
                                        <span class="ml-2">Sebelumnya</span>
                                    </a>
                                    <a href="{{ route(DBRoutes::projectSK, [$projectId]) }}" class="btn btn-outline-primary btn-sm">
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
    <script src="{{ asset('dist/js/upload-v2.js') }}"></script>
    <script type="text/javascript">
        const actionsSurkas = new Actions("{{ route(DBRoutes::projectInvestor, [$projectId]) }}");
        actionsSurkas.selectors.table = '#table-project-investor';
        actionsSurkas.datatable.params = {
            _token: "{{ csrf_token() }}",
        };
        actionsSurkas.datatable.columnDefs = [
            {
                targets: 0,
                width: 20,
                render: (data, type, row, meta) => {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
            },
            {
                targets: 4,
                render: (data) => {
                    const $wrapper = $('<div>', {class: 'text-center'});
                    $wrapper.html(data);

                    return $wrapper.get(0).outerHTML;
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
        actionsSurkas.build();
    </script>
@endpush
