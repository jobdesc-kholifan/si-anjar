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
                    <div class="row align-items-end">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="select-group">Role User</label>
                                <select
                                    id="select-group"
                                    class="form-control"
                                    data-toggle="select2"
                                    data-url="{{ route(DBRoutes::mastersTypesSelect) }}"
                                    data-params='{"parent_slug": "{{ DBTypes::role }}"}'
                                ></select>
                            </div>
                        </div>
                        <div class="col-sm-9 form-group">
                            <i class="fas fa-spinner fa-pulse fa-2x d-none" id="spinner"></i>
                            <button type="button" class="btn btn-outline-primary btn-md d-none" id="btn-save">
                                <i class="fa fa-check mr-1"></i>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    </div>
                    <div class="">
                        <table class="table" id="table-privileges"></table>
                    </div>
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

        const $btnSave = $("#btn-save");
        const $spinner = $('#spinner');
        const $tablePrivileges = $("#table-privileges");

        const menuCollection = new MenuCollection($tablePrivileges);

        $selectUserType.change(() => {
            $tablePrivileges.empty();
            $btnSave.addClass('d-none');
            $spinner.removeClass('d-none');

            loadPermission();
        });

        $btnSave.click(() => {
            $spinner.removeClass('d-none');
            $btnSave.addClass('d-none');

            ServiceAjax.post("{{ url()->current() }}", {
                data: {
                    role_id: $selectUserType.val(),
                    menus: JSON.stringify(menuCollection.items),
                }
            }).done((res) => {
                loadPermission();
                AlertNotif.toastr.response(res);
            });
        });

        function loadPermission() {
            ServiceAjax.get("{{ url()->current() }}/features", {
                data: {
                    role_id: $selectUserType.val()
                }
            }).done((res) => {
                if(res.result) {
                    $spinner.addClass('d-none');
                    $btnSave.removeClass('d-none');

                    menuCollection.setData(res.data);
                    menuCollection.init();
                    menuCollection.renderChild(0);
                }
            });
        }
    </script>
@endpush
