<?php

/**
 * @var ProjectCollection $project
 * */

use App\Helpers\Collections\Projects\ProjectCollection;

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
                                <h4 class="pt-3 pb-2 px-2 mb-4 border-bottom">
                                    Form Investor
                                    @if($isDraft)
                                        <div class="btn btn-primary btn-xs rounded-pill px-2 ml-1">Draft</div>
                                    @endif
                                </h4>
                                <div class="form-group">
                                    <dl class="row">
                                        <dt class="col-4 col-sm-2">Proyek</dt>
                                        <dd class="col-8 col-sm-10">{{ $project->getName() }}</dd>
                                        <dt class="col-4 col-sm-2">Nilai Proyek</dt>
                                        <dd class="col-8 col-sm-10">{{ IDR($project->getValue()) }}</dd>
                                        <dt class="col-4 col-sm-2">Jumlah Lembar Saham</dt>
                                        <dd class="col-8 col-sm-10">{{ number_format($project->getSharesValue(), 0, ",", ".") }} Lembar</dd>
                                        <dt class="col-4 col-sm-2">Modal Disetor</dt>
                                        <dd class="col-8 col-sm-10" id="label-modal-value">{{ IDR($project->getModalValue()) }}</dd>
                                        <dt class="col-4 col-sm-2">Kekurangan Modal Disetor</dt>
                                        <dd class="col-8 col-sm-10" id="label-modal-lack">{{ IDR($project->getValue() - $project->getModalValue()) }}</dd>
                                    </dl>
                                </div>
                                @if($hasUpdate)
                                    <div class="form-group d-flex justify-content-end align-items-center">
                                        @if($tabActive == 'sk')
                                            <a href="{{ route(DBRoutes::projectSK, [$projectId]) }}" class="btn btn-outline-secondary btn-sm mr-1">
                                                <i class="fa fa-angle-left mr-1"></i>
                                                <span>Kembali ke Halaman SK</span>
                                            </a>
                                        @endif
                                        <button type="button" class="btn btn-outline-secondary btn-sm mr-1" onclick="actions.setDraft()">
                                            <i class="fa fa-file-alt mr-1"></i>
                                            <span>Simpan Sebagai Draft</span>
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-sm" id="submit-form">
                                            <i class="fa fa-check-circle mr-1"></i>
                                            <span>Simpan</span>
                                        </button>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <div class="w-100">
                                        <table class="table table-striped table-hover w-100" id="table-project-investor">
                                            <thead>
                                            <tr>
                                                <th data-name="no" data-orderable="false" data-searchable="false">No</th>
                                                <th data-data="investor.no_ktp" data-name="investor.no_ktp">No. KTP</th>
                                                <th data-data="investor.investor_name" data-name="investor.investor_name">Nama Investor</th>
                                                <th data-data="investment_percentage" data-name="investment_value">Lembar Saham</th>
                                                <th data-data="investment_percentage" data-name="investment_value" class="text-right text-bold">Nominal</th>
                                                <th data-data="investment_percentage" data-name="investment_value" class="text-center">Porsi Saham</th>
                                                <th data-data="action" data-orderable="false" data-searchable="false" style="width: 200px">Aksi</th>
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
    <script src="{{ asset('dist/js/project-investor.js') }}"></script>
    <script type="text/javascript">

        const projectInvestor = new ProjectInvestor('#table-project-investor', {
            route: {
                investor: "{{ route(DBRoutes::investor) }}"
            },
            projectValue: {{ $project->getValue() }},
            sharesValue: {{ $project->getSharesValue() }},
        });

        const actions = {
            setDraft: function() {
                projectInvestor.options.isDraft = true;
                $('#submit-form').click();
            }
        };

        ServiceAjax.get("{{ route(DBRoutes::projectInvestorAll, [$projectId]) }}", {data: {isDraft: '{{ $isDraft }}'}})
            .done(res => {
                if(res.result) {
                    if(res.data.length === 0)
                        projectInvestor.add();

                    projectInvestor.set(res.data);
                }
            })

        const $form = $('#form-project').formSubmit({
            data: function(params) {
                params.investors = projectInvestor.toString();
                params.isDraft = projectInvestor.options.isDraft;
                return params;
            },
            beforeSubmit: function() {
                if(!projectInvestor.options.isDraft) {
                    if(confirm("Apakah data yang diinputkan sudah benar?")) {
                        projectInvestor.validate();
                        return projectInvestor.isValid();
                    }

                    return false;
                }

                projectInvestor.validate();
            },
            successCallback: function(res) {
                AlertNotif.toastr.response(res);

                if(res.result)
                    window.location.href = "{{ route(DBRoutes::projectInvestor, [$projectId]) }}";
            },
        });
    </script>
@endpush
