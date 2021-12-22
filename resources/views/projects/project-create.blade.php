@extends('skins.template')

@section('content')
    <x-content-header :title='$title' :breadcrumbs="$breadcrumbs"/>

    <section class="content">
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-body">
                    <form id="form-project" method="post">
                        {{ csrf_field() }}
                        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link{{ $tab == 'pic' ? ' active' : '' }}" id="pic" data-toggle="pill" href="#content-pic" role="tab" aria-selected="true">PIC</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link{{ $tab == 'project' ? ' active' : '' }}" id="project" data-toggle="pill" href="#content-project" role="tab" aria-selected="true">Proyek</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link disabled" id="investor" data-toggle="pill" href="#" role="tab" aria-selected="true">Investor</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link disabled" id="sk" data-toggle="pill" href="#" role="tab" aria-selected="true">SK</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link disabled" id="surkas" data-toggle="pill" href="#" role="tab" aria-selected="true">Surkas</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane{{ $tab == 'pic' ? ' active show' : '' }} fade" id="content-pic">
                                @include('projects.project-tab-pic')
                            </div>
                            <div class="tab-pane{{ $tab == 'project' ? ' active show' : '' }} fade" id="content-project">
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
    <script src="{{ asset('dist/js/upload-v2.js') }}"></script>
    <script type="text/javascript">
        FormComponents.daterangepicker.init();
        FormComponents.select2.init();
        FormComponents.number.init();

        const formPIC = new FormPIC('#form-pic');
        formPIC.add();

        const fileProposal = $('#file-proposal');
        fileProposal.upload({
            name: 'file_proposal',
            allowed: ['application/pdf'],
        });

        const fileBuktiTransfer = $('#file-bukti-transfer');
        fileBuktiTransfer.upload({
            name: 'file_bukti_transfer',
            allowed: ['image/*', 'application/pdf'],
        });

        const fileAttachment = $('#file-lampiran-project');
        fileAttachment.upload({
            name: 'file_lampiran[]',
            allowed: ['image/*'],
            multiple: true,
            withDescription: true,
        });

        const formProject = $('#form-project').formSubmit({
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

        const $inputValue = $('#input-value');
        const $inputShares = $('#input-shares');
        const $labelPrice = $('#harga-perlembar');

        const actions = {
            init: function() {
                $inputValue.donetyping(() => actions.calcuclate(), 0);
                $inputShares.donetyping(() => actions.calcuclate(), 0);

                actions.calcuclate();
            },
            calcuclate: function() {
                const price = parseInt($inputValue.val())/parseInt($inputShares.val());
                $labelPrice.html(`Harga per lembar Rp. ${$.number(price)}`);
            }
        };
        actions.init();
    </script>
@endpush
