<?php

/**
 * @var \App\Helpers\Collections\Projects\ProjectSurkasCollection $surkas
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
                    <dt class="col-sm-6">Nomor</dt>
                    <dd class="col-sm-6">{{ $surkas->getNoSurkas() }}</dd>
                    <dt class="col-sm-6">Tanggal</dt>
                    <dd class="col-sm-6">{{ $surkas->getSurkasDate() }}</dd>
                    <dt class="col-sm-6">Jumlah</dt>
                    <dd class="col-sm-6">{{ IDR($surkas->getSurkasValue()) }}</dd>
                    <dt class="col-sm-12">
                        Lampiran
                        <div id="file-lampiran" data-files="{{ $surkas->getFileAttachment()->toJson() }}"></div>
                    </dt>
                </dl>
            </div>
        </div>
    </div>
</div>
