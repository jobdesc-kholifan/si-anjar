<?php

use App\Helpers\Collections\Config\ConfigCollection;

/**
 * @var ConfigCollection $role
 * */

$hasAccessUpdate = findPermission(DBMenus::usersRole)->hasAccess(DBFeature::update);

?>
@extends('skins.template')

@section('content')
    <x-content-header :title='$title' :breadcrumbs="$breadcrumbs"/>

    <section class="content">
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-header">
                    <h3 class="card-title">Data {{ $title }}</h3>
                </div>
                <div class="card-body">
                    <form id="form-data" method="post">
                        <div class="row align-items-end">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="input-name" class="required">Nama Role</label>
                                    <input
                                        type="text"
                                        id="input-name"
                                        class="form-control"
                                        name="name"
                                        placeholder="{{ DBText::inputPlaceholder('Nama Role') }}"
                                        maxlength="100"
                                        value="{{ $role->getName() }}"
                                        required
                                    />
                                </div>
                            </div>
                            <div class="col-sm-9 form-group">
                                <i class="fas fa-spinner fa-pulse fa-2x d-none" id="spinner"></i>
                                @if($hasAccessUpdate)
                                    <button type="submit" class="btn btn-outline-primary btn-md">
                                        <i class="fa fa-check mr-1"></i>
                                        <span>Simpan Perubahan</span>
                                    </button>
                                @endif
                                <a href="{{ route(DBRoutes::usersRole) }}" class="btn btn-outline-primary">
                                    <span>Kembali</span>
                                </a>
                            </div>
                        </div>
                        <div class="">
                            <table class="table table-striped table-hover" id="table-privileges"></table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script-footer')
    <script src="{{ asset('dist/js/menu-collection.js') }}"></script>
    <script type="text/javascript">
        FormComponents.select2.init();

        const $selectUserType = $('#select-group');

        const $form = $("#form-data");
        const $spinner = $('#spinner');
        const $tablePrivileges = $("#table-privileges");

        const menuCollection = new MenuCollection($tablePrivileges, {
            slugs: {
                view: "{{ DBFeature::view }}",
            }
        });

        $form.formSubmit({
            data: function(params) {
                params.menus = menuCollection.toString();
                return params;
            },
            successCallback: function(res) {
                AlertNotif.toastr.response(res);
            }
        });

        function loadPermission() {
            ServiceAjax.get("{{ route(DBRoutes::usersRoleFeatures) }}", {
                data: {
                    role_id: "{{ $role->getId() }}"
                }
            }).done((res) => {
                if(res.result) {
                    $spinner.addClass('d-none');

                    menuCollection.setData(res.data);
                    menuCollection.init();
                    menuCollection.renderChild(0);
                }
            });
        }

        loadPermission();
    </script>
@endpush
