<?php

$hasAccessCreate = findPermission(DBMenus::investor)->hasAccess(DBFeature::create);

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
                                <th data-data="no_ktp" data-name="no_ktp">No. KTP</th>
                                <th data-data="investor_name" data-name="investor_name">Nama Investor</th>
                                <th data-data="created_at" data-name="created_at">Tgl Bergabung</th>
                                <th data-data="phone_number" data-name="phone_number">No. Handphone</th>
                                <th data-data="phone_number" data-name="phone_number">Jumlah Proyek</th>
                                <th data-data="phone_number" data-name="phone_number">Total Investasi</th>
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
    <script src="{{ asset('dist/js/investor-bank.js') }}"></script>
    <script src="{{ asset('dist/js/upload.js') }}"></script>
    <script type="text/javascript">
        let formBank, fileKTP, fileNPWP;
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
        actions.callback.onCreate = function(res, form) {
            formBank = new FormBank('#form-bank', {
                selectBank: "{{ route(DBRoutes::mastersBankSelect) }}"
            });
            formBank.add();
        };
        actions.callback.modal.onLoadComplete = function(modal) {
            formBank = new FormBank('#form-bank', {
                selectBank: "{{ route(DBRoutes::mastersBankSelect) }}"
            });
            fileKTP = $('#upload-ktp').upload({
                name: 'file_ktp',
                allowed: ['image/*'],
                getMimeType: (file) => file.mime_type,
                getPreview: (file) => file.preview
            });
            fileNPWP = $('#upload-npwp').upload({
                name: 'file_npwp',
                allowed: ['image/*'],
                getMimeType: (file) => file.mime_type,
                getPreview: (file) => file.preview
            });
        };
        actions.callback.onCreate = function() {
            formBank.add();
        };
        actions.callback.onEdit = function(data) {
            formBank.set(data.banks);
            console.log(formBank);
            fileKTP.set(data.file_ktp);
            fileNPWP.set(data.file_npwp);
        };
        actions.callback.form.onSetData = function(value, key, row, form) {
            const $el = form.find(`[name="${key}"]`);
            if(value != null && ['religion_id', 'relationship_id'].includes(key)) {
                const data = row[key.replace('_id', '')];
                $el.append($('<option>', {value: data.id}).text(data.name));
            }

            else if(key === 'gender_id') {
                form.find(`[name=${key}]`).each((i, option) => {
                    const $option = $(option);
                    if($option.attr('value') === value.toString()) {
                        $option.prop('checked', true);
                    }
                });

            }
        };
        actions.callback.form.appendData = function(params) {
            params.banks = formBank.toString();
            return params;
        }
        actions.build();
    </script>
@endpush
