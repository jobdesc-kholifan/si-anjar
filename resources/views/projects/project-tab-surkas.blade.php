<?php

/**
 * @var ProjectCollection $project
 * */

use App\Helpers\Collections\Projects\ProjectCollection;

$hasCreate = findPermission(DBMenus::project)->hasAccess(DBFeature::create);
?>
@extends('skins.template')

@section('content')
<x-content-header :title='$title' :breadcrumbs="$breadcrumbs" />

<section class="content">
    <div class="container-fluid">
        <div class="card shadow">
            <div class="card-body">
                <form id="form-project" method="post">
                    {{ csrf_field() }}
                    @include('projects.project-tab-menu')

                    <div class="tab-content">
                        <h4 class="pt-3 pb-2 px-2 mb-4 border-bottom">Form Surkas</h4>
                        <div class="form-group">
                            <dl class="row">
                                <dt class="col-4 col-sm-2">Proyek</dt>
                                <dd class="col-8 col-sm-10">{{ $project->getName() }}</dd>
                                <dt class="col-4 col-sm-2">Total Surkas Dibagikan</dt>
                                <dd class="col-8 col-sm-10" data-name="total">Rp. 0</dd>
                            </dl>
                        </div>
                        <div class="form-group text-right">
                            @if($hasCreate)
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="actionsSurkas.create()">
                                <i class="fa fa-plus-circle"></i>
                                <span class="ml-2">Tambah Surkas</span>
                            </button>
                            @endif
                        </div>
                        <div class="form-group">
                            <div class="w-100">
                                <table class="table table-striped table-hover w-100" id="table-project-surkas">
                                    <thead>
                                        <tr>
                                            <th data-data="surkas_no" data-name="surkas_no">No. Surkas</th>
                                            <th data-data="surkas_date" data-name="surkas_date">Tanggal Surkas</th>
                                            <th data-data="surkas_value" data-name="surkas_value" class="text-right">Jumlah</th>
                                            <th data-data="admin_fee" data-name="admin_fee" class="text-right">Biaya Admin</th>
                                            <th data-data="status.name" data-name="status.name">Status</th>
                                            <th data-data="action" data-orderable="false" data-searchable="false" style="width: 200px">Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="footer-actions">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="$('#sk').trigger('click')">
                                <i class="fa fa-angle-left"></i>
                                <span class="ml-2">Sebelumnya</span>
                            </button>
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="fa fa-save"></i>
                                <span class="mr-2">Simpan</span>
                            </button>
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
        let fileLampiranSurkas;
        const $total = $('[data-name=total]');
        const projectValue = {{ $project->getValue() }};
        const actionsSurkas = new Actions("{{ route(DBRoutes::projectSurkas, [$projectId]) }}");
        actionsSurkas.selectors.table = '#table-project-surkas';
        actionsSurkas.datatable.params = {
            _token: "{{ csrf_token() }}",
        };
        actionsSurkas.datatable.order = [[0, 'desc']];
        actionsSurkas.callback.modal.onLoadComplete = function() {
            fileLampiranSurkas = $('#file-lampiran-surkas').upload({
                name: 'file_lampiran_surkas[]',
                allowed: ['image/*'],
                multiple: true,
                withDescription: true,
                getMimeType: (file) => file.mime_type,
                getPreview: (file) => file.preview,
                getDesc: (file) => file.description,
            });

            FormComponents.number.init();
        };
        actionsSurkas.callback.onEdit = function(data){
            fileLampiranSurkas.set(data.file_lampiran_surkas);
        };
        actionsSurkas.callback.form.onSuccessCallback = function() {
            actionsSurkas.totalSurkas();
        };
        actionsSurkas.callback.onDelete = function(data) {
            actionsSurkas.totalSurkas();
        }
        actionsSurkas.totalSurkas = function() {
            ServiceAjax.get("{{ route(DBRoutes::projectSurkasTotal, [$projectId]) }}")
                .done((res) => {
                    if(res.result) {
                        $total.html(res.data);
                    }
                })
        };
        actionsSurkas.approved = function(id) {
            $.confirmModal({
                onChange: (value, modal) => {
                    if(value) {
                        modal.disabled(true);
                        ServiceAjax.post(`{{ route(DBRoutes::projectSurkas, [$projectId]) }}/approved`, {
                            data: {id: id},
                        }).done((res) => {
                            if(res.result) {
                                modal.close();
                                actionsSurkas.datatable.reload();
                            }

                            modal.disabled(false);
                            AlertNotif.toastr.response(res);
                        });
                    } else modal.close();
                },
            }).show();
        };
        actionsSurkas.detail = function(id) {
            $.createModal({
                url: '{{ url()->current() }}/detail',
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
        actionsSurkas.build();
        actionsSurkas.totalSurkas();
    </script>
@endpush
