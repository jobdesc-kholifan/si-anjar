<?php

use App\Helpers\Collections\Menu\MenuCollection;

/**
 * @var MenuCollection $row
*/

$hasAccessCreateFeature = findPermission(DBMenus::securityMenu)->hasAccess('create-feature');

?>
@extends('skins.template')

@section('content')
    <x-content-header :title='$title' :breadcrumbs="$breadcrumbs"/>

    <section class="content">
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <h5 class="border-bottom pb-3 mb-3">
                                Informasi Menu
                            </h5>
                            <form id="form-header">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="select-parent">Parent</label>
                                    <select
                                        id="select-parent"
                                        class="form-control"
                                        name="parent_id"
                                        data-toggle="select2"
                                        data-url="{{ route(DBRoutes::securityMenuSelect) }}"
                                    >
                                        @if(!empty($row->getParentId()))
                                            <option value="{{ $row->getParentId() }}">{{ $row->getParent()->getName() }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="input-name" class="required">Nama Menu</label>
                                    <input
                                        type="text"
                                        id="input-name"
                                        class="form-control"
                                        name="name"
                                        placeholder="{{ DBText::inputPlaceholder('Nama Menu') }}"
                                        maxlength="100"
                                        value="{{ $row->getName() }}"
                                        required
                                    />
                                </div>
                                <div class="form-group">
                                    <label for="input-slug" class="required">Slug</label>
                                    <input
                                        type="text"
                                        id="input-slug"
                                        class="form-control"
                                        name="slug"
                                        placeholder="{{ DBText::inputPlaceholder("Slug") }}"
                                        value="{{ $row->getSlug() }}"
                                        required
                                    />
                                </div>
                                <div class="form-group">
                                    <label for="input-icon">Icon</label>
                                    <input
                                        type="text"
                                        id="input-icon"
                                        class="form-control"
                                        name="icon"
                                        placeholder="{{ DBText::inputPlaceholder("Icon") }}"
                                        value="{{ $row->getIcon() }}"
                                    />
                                </div>
                                <div class="form-group">
                                    <label for="input-sequence">Urutan</label>
                                    <input
                                        id="input-sequence"
                                        class="form-control"
                                        name="sequence"
                                        placeholder="0"
                                        onkeydown="return Helpers.isNumberKey(event)"
                                        maxlength="5"
                                        value="{{ $row->getSequence() }}"
                                    />
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block btn-md">
                                        <i class="fa fa-save mr-1"></i>
                                        <span>Simpan Perubahan</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-sm-9">
                            <h5 class="border-bottom d-flex justify-content-between align-items-center pb-3 mb-3">
                                Fitur Fitur
                                <div class="">
                                    <a href="{{ route(DBRoutes::securityMenu) }}" class="btn btn-link btn-xs">
                                        <i class="fa fa-angle-left"></i>
                                        <span>Kembali</span>
                                    </a>
                                    @if($hasAccessCreateFeature)
                                    <button type="button" class="btn btn-primary btn-xs" onclick="actions.create()">
                                        <i class="fa fa-plus-circle"></i>
                                        <span>Tambah Fitur</span>
                                    </button>
                                    @endif
                                </div>
                            </h5>
                            <div class="w-100">
                                <table class="table table-striped table-hover" id="table-data">
                                    <thead>
                                    <tr>
                                        <th data-name="no" data-orderable="false" data-searchable="false">No</th>
                                        <th data-data="title" data-name="title">Fitur</th>
                                        <th data-data="slug" data-name="slug">Slug</th>
                                        <th data-data="description" data-name="description">Deskripsi</th>
                                        <th data-data="action" data-orderable="false" data-searchable="false">Aksi</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script-footer')
    <script src="{{ asset('dist/js/actions-v2.js') }}"></script>
    <script type="text/javascript">
        FormComponents.select2.init();

        const actions = new Actions("{{ url()->current() }}/features");
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

        $('#form-header').formSubmit({
            url: "{{ url()->current() }}",
            method: "post",
            successCallback: function(res) {
                AlertNotif.toastr.response(res);
            }
        })
    </script>
@endpush
