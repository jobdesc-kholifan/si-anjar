<form>
    {{ csrf_field() }}
    <div class="modal-header">
        <h3 class="card-title">Form Investor</h3>
        <span class="close" data-dismiss="modal">&times;</span>
    </div>
    <div class="modal-body p-0">
        <div class="px-3 mt-3">
            <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="kotak" data-toggle="pill" href="#content-kontak" role="tab" aria-selected="true">Kontak</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pribadi" data-toggle="pill" href="#content-pribadi" role="tab" aria-selected="false">Pribadi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="bank" data-toggle="pill" href="#content-bank" role="tab" aria-selected="false">Bank</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="darurat" data-toggle="pill" href="#content-darurat" role="tab" aria-selected="false">Darurat</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="lampiran" data-toggle="pill" href="#content-lampiran" role="tab" aria-selected="false">Lapiran</a>
                </li>
            </ul>
        </div>
        <div class="tab-content">
            <div class="tab-pane active show fade" id="content-kontak">
                @include('investors.modal-form-kontak')
            </div>
            <div class="tab-pane fade" id="content-pribadi">
                @include('investors.modal-form-pribadi')
            </div>
            <div class="tab-pane fade" id="content-bank">
                @include('investors.modal-form-bank')
            </div>
            <div class="tab-pane fade" id="content-darurat">
                @include('investors.modal-form-darurat')
            </div>
            <div class="tab-pane fade" id="content-lampiran">
                @include('investors.modal-form-lampiran')
            </div>
        </div>
    </div>
</form>
