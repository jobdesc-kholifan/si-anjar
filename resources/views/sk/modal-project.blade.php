<?php

/**
 * @var \App\Helpers\Collections\Projects\ProjectCollection $project
 * */

?>
<div class="modal-header">
    <h3 class="card-title">Informasi Proyek</h3>
    <span class="close" data-dismiss="modal">&times;</span>
</div>
<div class="modal-body">
    <div class="list-information">
        <div class="row">
            <div class="col-sm-8">
                <dl class="row dt-bold">
                    <dt class="col-sm-6">Nama Project</dt>
                    <dd class="col-sm-6">{{ $project->getName() }}</dd>
                    <dt class="col-sm-6">Kategori</dt>
                    <dd class="col-sm-6">{{ $project->getCategory()->getName() }}</dd>
                    <dt class="col-sm-6">Nilai Project</dt>
                    <dd class="col-sm-6">{{ $project->getValue(true) }}</dd>
                    <dt class="col-sm-6">Lembar Saham</dt>
                    <dd class="col-sm-6">{{ $project->getSharesValue(true) }}</dd>
                    <dt class="col-sm-6">Tanggal Mulai</dt>
                    <dd class="col-sm-6">{{ $project->getStartDate() }}</dd>
                    <dt class="col-sm-6">Tanggal Selesai</dt>
                    <dd class="col-sm-6">{{ $project->getFinishDate() }}</dd>
                    <dt class="col-sm-6">Proyeksi Keuntungan</dt>
                    <dd class="col-sm-6">{{ $project->getEstimateProfitValue() }}</dd>
                    <dt class="col-sm-6">Proposal Proyek</dt>
                    <dd class="col-sm-6"><a href="{{ $project->getFileProposal()->getPreview() }}" target="_blank">{{ $project->getFileProposal()->getFileName() }}</a></dd>
                    <dt class="col-sm-12">
                        Bukti Transfer
                        <div id="file-bukti-transfer" data-files="{{ $project->getFileEvidence()->toJson() }}"></div>
                    </dt>
                    <dt class="col-sm-12">
                        Lampiran
                        <div id="file-lampiran-project" data-files="{{ $project->getFileAttachment()->toJson() }}"></div>
                    </dt>
                </dl>
            </div>
        </div>
    </div>
</div>
