<?php

/**
 * @var \App\Helpers\Collections\Projects\ProjectSKCollection $sk
 * */

?>
<div class="modal-header">
    <h3 class="card-title">Informasi SK</h3>
    <span class="close" data-dismiss="modal">&times;</span>
</div>
<div class="modal-body">
    <div class="list-information">
        <div class="row">
            <div class="col-sm-8">
                <dl class="row dt-bold">
                    <dt class="col-sm-6">No. SK</dt>
                    <dd class="col-sm-6">{{ $sk->getNoSK() }}</dd>
                    <dt class="col-sm-6">Revision</dt>
                    <dd class="col-sm-6">{{ $sk->getRevision() }}</dd>
                    <dt class="col-sm-6">Status</dt>
                    <dd class="col-sm-6">{{ $sk->getStatus()->getName() }}</dd>
                    <dt class="col-sm-6">Tanggal Cetak</dt>
                    <dd class="col-sm-6">{{ $sk->getPrintedAt() }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
