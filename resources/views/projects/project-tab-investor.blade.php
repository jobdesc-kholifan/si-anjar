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
                                <h4 class="pt-3 pb-2 px-2 mb-4 border-bottom">Form Investor</h4>
                                <div class="form-group">
                                    <dl class="row">
                                        <dt class="col-4 col-sm-2">Proyek</dt>
                                        <dd class="col-8 col-sm-10">{{ $project->getName() }}</dd>
                                        <dt class="col-4 col-sm-2">Nilai Proyek</dt>
                                        <dd class="col-8 col-sm-10">{{ IDR($project->getValue()) }}</dd>
                                        <dt class="col-4 col-sm-2">Modal Disetor</dt>
                                        <dd class="col-8 col-sm-10" id="label-modal-value">{{ IDR($project->getModalValue()) }}</dd>
                                        <dt class="col-4 col-sm-2">Kekurangan Modal Disetor</dt>
                                        <dd class="col-8 col-sm-10" id="label-modal-lack">{{ IDR($project->getValue() - $project->getModalValue()) }}</dd>
                                    </dl>
                                </div>
                                <div class="form-group text-right">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="actionsInvestor.create()">
                                        <i class="fa fa-plus-circle"></i>
                                        <span class="ml-2">Tambah Investor</span>
                                    </button>
                                </div>
                                <div class="form-group">
                                    <div class="w-100">
                                        <table class="table table-striped table-hover w-100" id="table-project-investor">
                                            <thead>
                                            <tr>
                                                <th data-name="no" data-orderable="false" data-searchable="false">No</th>
                                                <th data-data="investor.no_ktp" data-name="investor.no_ktp">NIK</th>
                                                <th data-data="investor.investor_name" data-name="investor.investor_name">Nama Investor</th>
                                                <th data-data="investment_percentage" data-name="investment_value">Porsi Saham</th>
                                                <th data-data="investment_value" data-name="investment_value" class="text-right">Nominal Disetor</th>
                                                <th data-data="action" data-orderable="false" data-searchable="false" style="width: 200px">Aksi</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                                <div class="footer-actions">
                                    <a href="{{ route(DBRoutes::projectEdit, [$projectId]) }}?tab=proyek" class="btn btn-outline-secondary btn-sm" onclick="$('#project').trigger('click')">
                                        <i class="fa fa-angle-left"></i>
                                        <span class="ml-2">Sebelumnya</span>
                                    </a>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="$('#sk').trigger('click')">
                                        <span class="mr-2">Selanjutnya</span>
                                        <i class="fa fa-angle-right"></i>
                                    </button>
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
        const projectValue = {{ $project->getValue() }};
        const actionsInvestor = new Actions("{{ route(DBRoutes::projectInvestor, [$projectId]) }}");
        actionsInvestor.selectors.table = '#table-project-investor';
        actionsInvestor.datatable.params = {
            _token: "{{ csrf_token() }}",
        };
        actionsInvestor.datatable.columnDefs = [
            {
                targets: 0,
                width: 20,
                render: (data, type, row, meta) => {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
            }
        ];
        actionsInvestor.callback.form.onSuccessCallback = function(res) {
            actionsInvestor.updateInfoProject();
        };
        actionsInvestor.callback.modal.onLoadComplete = function() {
            $('#input-nominal').donetyping((value) => actionsInvestor.calculatePercentage($(value).val()));
        };
        actionsInvestor.callback.form.appendData = function(params) {
            params.project_id = "{{ $projectId }}";
            return params;
        };
        actionsInvestor.callback.form.onSetData = function(value, key, row, form) {
            const $el = $(`[name="${key}"]`);
            if(value !== null && ['investor_id'].includes(key)) {
                const data = row[key.replace('_id', '')];
                $el.append($('<option>', {value: data.id}).text(data.investor_name));
            }

            actionsInvestor.calculatePercentage(row.investment_value);
        };
        actionsInvestor.callback.onDelete = function() {
            actionsInvestor.updateInfoProject();
        };
        actionsInvestor.updateInfoProject = function() {
            ServiceAjax.get("{{ route(DBRoutes::projectShow, [$projectId]) }}")
                .done(res => {
                    if(res.result) {
                        const modalValue = res.data.modal_value;

                        const $labelModalValue = $('#label-modal-value');
                        $labelModalValue.html(`Rp. ${$.number(modalValue, null, ',', '.')}`);

                        const $labelModalLack = $("#label-modal-lack");
                        $labelModalLack.html(`Rp. ${$.number(projectValue - modalValue, null, ',', '.')}`);
                    }
                });
        };
        actionsInvestor.calculatePercentage = function(value) {
            const percentage = value/projectValue * 100;
            const $percentage = $('#label-porsi');
            return $percentage.html(`Porsi Saham: ${percentage > 100 ? 100 : percentage}%`);
        }
        actionsInvestor.build();
    </script>
@endpush
