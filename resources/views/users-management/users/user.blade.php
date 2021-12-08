<?php

$hasAccessCreate = findPermission(DBMenus::usersUser)->hasAccess(DBFeature::create);
$hasAccessDelete = findPermission(DBMenus::usersUser)->hasAccess(DBFeature::delete);

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
                        @if($hasAccessDelete)
                        <button type="button" class="btn btn-danger btn-sm d-none" id="btn-multiple-delete" onclick="actions.multipleDelete()">
                            <i class="fa fa-trash"></i>
                            <span>Hapus</span>
                        </button>
                        @endif
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
                                <th data-data="full_name" data-name="full_name">Nama Lengkap</th>
                                <th data-data="email_phone" data-name="email">Email/No Telp</th>
                                <th data-data="user_name" data-name="user_name">Nama Pengguna</th>
                                <th data-data="role.name" data-name="role_id">Role</th>
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
        let timeout = null;
        let deletedCheckbox = [];
        let deletedName = [];

        const actions = new Actions("{{ url()->current() }}");
        actions.datatable.params = {
            _token: "{{ csrf_token() }}",
        };
        actions.callback.form.onSetData = function(value, key, row, form) {
            const $el = form.find(`[name="${key}"]`);
            if(value != null && ['role_id'].includes(key)) {
                const data = row[key.replace('_id', '')];
                $el.append($('<option>', {value: data.id}).text(data.name));
            }

        };
        actions.detail = function(id) {
            $.createModal({
                url: '{{ url()->current() }}/detail',
                data: {
                    id: id
                },
            }).open();
        };
        actions.checkboxMultipleDelete = function() {
            let $checkbox = $('[name="checkbox_delete[]"]');
            let $btnMultipleDelete = $('#btn-multiple-delete');

            if($checkbox.length > 0) {

                for(let i = 0; i < $checkbox.length; i++) {
                    let $current = $($checkbox[i]);
                    if(deletedCheckbox.includes($current.val()))
                        $current.prop('checked', true);
                }

                for(let i = 0; i < $checkbox.length; i++) {
                    let $current = $($checkbox[i]);
                    $current.click(function() {
                        let fullName = $current.closest('label').find('#fullName').text();

                        if($current.prop('checked')
                            && !deletedCheckbox.includes($current.val()))
                            deletedCheckbox.push($current.val());

                        if($current.prop('checked')
                            && !deletedName.includes(fullName))
                            deletedName.push(fullName);

                        if(!$current.prop('checked')
                            && deletedCheckbox.includes($current.val()))
                            deletedCheckbox.splice(deletedCheckbox.indexOf($current.val()), 1);

                        if(!$current.prop('checked')
                            && deletedName.includes(fullName))
                            deletedName.splice(deletedName.indexOf(fullName), 1);
                    });
                }

                if(deletedCheckbox.length > 0) {
                    $btnMultipleDelete.removeClass('d-none');
                    $btnMultipleDelete.find('span').text(`Hapus (${deletedCheckbox.length})`)
                }

                else $btnMultipleDelete.addClass('d-none');
            }

            timeout = setTimeout(function() {
                actions.checkboxMultipleDelete();
            }, 100)
        };
        actions.multipleDelete = function() {
            let $btnMultipleDelete = $('#btn-multiple-delete');

            $.confirmModal({
                message: 'Apakah anda yakin anda akan menghapus data <b>' + deletedName.join(", ") + '</b>',
                onChange: (value, modal) => {
                    if(value) {
                        modal.disabled(true);
                        ServiceAjax.post("{{ url()->current() }}/multiple-delete", {
                            data: {ids: deletedCheckbox},
                            success: function(res) {
                                modal.close();

                                if(res.result) {
                                    deletedCheckbox = [];
                                    deletedName = [];
                                    $btnMultipleDelete.addClass('d-none');
                                    actions.datatable.reload();
                                    actions.checkboxMultipleDelete();
                                }
                            }
                        });
                    } else modal.close();
                },
            }).show();
        };

        actions.build();
        actions.checkboxMultipleDelete();
    </script>
@endpush
