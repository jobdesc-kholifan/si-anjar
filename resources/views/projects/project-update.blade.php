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
                            <div class="tab-pane{{ $tab == 'pic' ? ' active show' : '' }} fade" id="content-pic">
                                @include('projects.project-tab-pic')
                            </div>
                            <div class="tab-pane{{ $tab == 'proyek' ? ' active show' : '' }} fade" id="content-project">
                                @include('projects.project-tab-info')
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
    <script src="{{ asset('dist/js/project-pic.js') }}"></script>
    <script src="{{ asset('dist/js/upload.js') }}"></script>
    <script type="text/javascript">
        FormComponents.daterangepicker.init();
        FormComponents.select2.init();
        FormComponents.number.init();

        const $form = $('#form-project');
        const formPIC = new FormPIC('#form-pic');

        const fileProposal = $('#file-proposal');
        fileProposal.upload({
            name: 'file_proposal',
            allowed: ['image/*'],
            getMimeType: (file) => file.mime_type,
            getPreview: (file) => file.preview
        });

        const fileBuktiTransfer = $('#file-bukti-transfer');
        fileBuktiTransfer.upload({
            name: 'file_bukti_transfer',
            allowed: ['image/*'],
            getMimeType: (file) => file.mime_type,
            getPreview: (file) => file.preview
        });

        const fileAttachment = $('#file-lampiran-project');
        fileAttachment.upload({
            name: 'file_lampiran[]',
            allowed: ['image/*'],
            multiple: true,
            withDescription: true,
            getMimeType: (file) => file.mime_type,
            getPreview: (file) => file.preview,
            getDesc: (file) => file.description
        });

        ServiceAjax.get("{{ route(DBRoutes::projectShow, [$projectId]) }}")
            .done(function(res) {
                if(res.result) {
                    formPIC.set(res.data.data_pic);
                    fileProposal.data('upload').set(res.data.file_proposal);
                    fileBuktiTransfer.data('upload').set(res.data.file_bukti_transfer);
                    fileAttachment.data('upload').set(res.data.file_attachment);

                    Object.keys(res.data).forEach(key => {
                        const $el = $form.find(`[name="${key}"]`);
                        if(['project_category_id', 'estimate_profit_id'].includes(key)) {
                            const data = res.data[key.replace('_id', '')];
                            $el.append($('<option>', {value: data.id}).text(data.name));
                        }
                        else if($el !== undefined) {
                            if($el.is('input') && ['file'].includes($el.attr('type'))) {}

                            else $el.val(res.data[key]);

                        }
                    });
                } else window.location.href = "{{ route(DBRoutes::project) }}";
            });

        const formProject = $form.formSubmit({
            data: function(params) {
                params.data_pic = formPIC.toString();
                return params;
            },
            beforeSubmit: function(form) {
                form.setDisabled(true);
            },
            successCallback: function(res, form) {
                form.setDisabled(false);

                if(res.result) {
                    if(res.data.redirect !== undefined) {
                        window.location.href = res.data.redirect;
                    }
                }

                AlertNotif.toastr.response(res);
            },
            errorCallback: function(xhr, form) {
                form.setDisabled(false);

                AlertNotif.adminlte.error(DBMessage.ERROR_SYSTEM_MESSAGE, {
                    title: DBMessage.ERROR_PROCESSING_TITLE,
                    autoHide: false
                });
            }
        });
    </script>
@endpush
