<?php

/**
 * @var \App\Helpers\Collections\Projects\ProjectCollection $project
 * @var \App\Helpers\Collections\Projects\ProjectSKCollection $sk
 * */

?>
<form action="{{ url()->current() }}" method="post">
    {{ csrf_field() }}
    <div class="modal-header">
        <h3 class="card-title">Pengaturan Dokumen</h3>
        <span class="close" data-dismiss="modal">&times;</span>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label for="input-title" class="required">Judul</label>
            <input
                type="text"
                id="input-title"
                class="form-control"
                name="title"
                placeholder="{{ DBText::inputPlaceholder('Judul') }}"
                required
                value="{{ $sk->getPdfPayload()->getTitle($project->getName()) }}"
            />
        </div>
        <div class="form-group">
            <label for="input-address" class="required">Alamat</label>
            <textarea
                class="form-control"
                id="input-address"
                name="address"
                rows="5"
                placeholder="{{ DBText::inputPlaceholder("Alamat") }}"
            >{{ $sk->getPdfPayload()->getAddress("Jl. Bantar Gebang Setu, Kel. Padurenan, Kec. Mustika Jaya, Kota Bekasi, Jawa Barat") }}</textarea>
        </div>
        <div class="form-group">
            <label for="input-nomor" class="required">Nomor</label>
            <input
                type="text"
                id="input-nomor"
                class="form-control"
                name="nomor"
                placeholder="{{ DBText::inputPlaceholder('Nomor') }}"
                required
                value="{{ $sk->getPdfPayload()->getNoDocument(sprintf("%03d/SK/SRM/X/2021", $sk->getRevision())) }}"
            />
        </div>
        <div class="form-group">
            <label for="input-lampiran" class="required">Jumlah Lampiran</label>
            <input
                type="text"
                id="input-lampiran"
                class="form-control"
                name="number_of_attachment"
                placeholder="{{ DBText::inputPlaceholder('Jumlah Lampiran') }}"
                value="{{ $sk->getPdfPayload()->getNumberOfAttachment() }}"
            />
        </div>
        <div class="form-group">
            <label for="input-regards" class="required">Perihal</label>
            <input
                type="text"
                id="input-regards"
                class="form-control"
                name="regards"
                placeholder="{{ DBText::inputPlaceholder('Perihal') }}"
                required
                value="{{ $sk->getPdfPayload()->getRegards(sprintf("Kerjasama %s", $project->getName())) }}"
            />
        </div>
        <div class="form-group">
            <label for="input-place" class="required">Tempat dan Tanggal</label>
            <div class="row">
                <div class="col-sm-8">
                    <input
                        type="text"
                        id="input-place"
                        class="form-control"
                        name="place"
                        placeholder="{{ DBText::inputPlaceholder('Tempat Lahir') }}"
                        maxlength="100"
                        value="{{ $sk->getPdfPayload()->getPlace("Jakarta") }}"
                    />
                </div>
                <div class="col-sm-4">
                    <label for="input-ate" class="d-none"></label>
                    <input
                        type="text"
                        id="input-ate"
                        class="form-control"
                        name="date"
                        placeholder="{{ DBText::datePlaceholder() }}"
                        data-toggle="daterangepicker"
                        data-format="DD/MM/YYYY"
                        data-single-date="true"
                        data-auto-apply="true"
                        data-show-dropdowns="true"
                        data-disabled-next-date="false"
                        value="{{ $sk->getPdfPayload()->getDate() }}"
                    />
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="input-content" class="required">Isi Surat</label>
            <textarea
                class="form-control"
                id="input-content"
                name="content"
                rows="10"
                placeholder="{{ DBText::inputPlaceholder("Isi Surat") }}"
            >{{ $sk->getPdfPayload()->getContent() }}</textarea>
        </div>
        <div class="form-group">
            <label for="file-ttd-sk" class="required">TTD</label>
            <div id="file-ttd-sk" data-files="{{ $sk->getPdfPayload()->getSignatureJson() }}"></div>
        </div>
        <div class="form-group">
            <label for="input-signature" class="required">Nama TTD</label>
            <input
                type="text"
                id="input-signature"
                class="form-control"
                name="signature_name"
                placeholder="{{ DBText::inputPlaceholder('Nama TTD') }}"
                required
                value="{{ $sk->getPdfPayload()->getSignatureName() }}"
            />
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-danger btn-sm" data-dismiss="modal">
            <span>Batal</span>
        </button>
        <button type="submit" class="btn btn-outline-primary btn-sm">
            <span>Cetak Dokumen</span>
        </button>
    </div>
</form>
