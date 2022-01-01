<div class="modal-header">
    <h3 class="card-title">Informasi Detail</h3>
    <span class="close" data-dismiss="modal">&times;</span>
</div>
<div class="modal-body">
    <div class="list-information">
        <div class="row">
            <div class="col-sm-8">
                <dl class="row dt-bold">
                    <dt class="col-sm-6">Nama Project</dt>
                    <dd class="col-sm-6">{{ $project->project_name }}</dd>
                    <dt class="col-sm-6">Kategori</dt>
                    <dd class="col-sm-6">{{ $project->project_category->name }}</dd>
                    <dt class="col-sm-6">Nilai Project</dt>
                    <dd class="col-sm-6">{{ $project->project_value }}</dd>
                    <dt class="col-sm-6">Lembar Saham</dt>
                    <dd class="col-sm-6">{{ $project->project_shares }}</dd>
                    <dt class="col-sm-6">Tanggal Mulai</dt>
                    <dd class="col-sm-6">{{ $project->start_date }}</dd>
                    <dt class="col-sm-6">Tanggal Selesai</dt>
                    <dd class="col-sm-6">{{ date('d/m/Y', strtotime($project->finish_date)) }}</dd>
                    <dt class="col-sm-6">Proyeksi Keuntungan</dt>
                    <dd class="col-sm-6">{{ $project->estimate_profit_value . ' - ' . $project->estimate_profit->name }}</dd>
                    <dt class="col-sm-6">Proposal Proyek</dt>
                    <dd class="col-sm-6" id="file-proposal">
                        @if ($project->file_proposal->mime_type == 'application/pdf')
                            <a href="{{ $project->file_proposal->preview }}">
                                <div style="width: 150px; height: 200px; background-color: #f1f1f1; border-radius: 5px; display: flex; justify-content: center; align-items: center; background-position: center; background-repeat: no-repeat; background-size: 100% auto; background-image:url({{ $project->file_proposal->preview }})"></div>
                            </a>
                        @else
                            <div style="width: 150px; height: 200px; background-color: #f1f1f1; border-radius: 5px; display: flex; justify-content: center; align-items: center; background-position: center; background-repeat: no-repeat; background-size: 100% auto; background-image:url({{ $project->file_proposal->preview }})"></div>
                        @endif
                    </dd>
                    <dt class="col-sm-6">Bukti Transfer Proyek</dt>
                    <dd class="col-sm-6" id="file-bukti-transfer">
                        @if ($project->file_bukti_transfer->mime_type == 'application/pdf')
                            <a href="{{ $project->file_bukti_transfer->preview }}">
                                <div style="width: 150px; height: 200px; background-color: #f1f1f1; border-radius: 5px; display: flex; justify-content: center; align-items: center; background-position: center; background-repeat: no-repeat; background-size: 100% auto;background-image:url({{ $project->file_bukti_transfer->preview }})"></div>
                            </a>
                        @else
                            <div style="width: 150px; height: 200px; background-color: #f1f1f1; border-radius: 5px; display: flex; justify-content: center; align-items: center; background-position: center; background-repeat: no-repeat; background-size: 100% auto;background-image:url({{ $project->file_bukti_transfer->preview }})"></div>
                        @endif
                    </dd>
                    <dt class="col-sm-6">Lampiran</dt>
                    <dd class="col-sm-6" id="file-lampiran-project">
                        @foreach ($project->file_attachment as $lampiran)  
                            @if ($lampiran->mime_type == 'application/pdf')
                                <a href="{{ $lampiran->preview }}">
                                    <div style="width: 150px; height: 200px; background-color: #f1f1f1; border-radius: 5px; display: flex; justify-content: center; align-items: center; background-position: center; margin-bottom: 10px; background-repeat: no-repeat; background-size: 100% auto; background-image:url({{ $lampiran->preview }})"></div>
                                </a>
                            @else
                                <div style="width: 150px; height: 200px; background-color: #f1f1f1; border-radius: 5px; display: flex; justify-content: center; align-items: center; background-position: center; margin-bottom: 10px; background-repeat: no-repeat; background-size: 100% auto; background-image:url({{ $lampiran->preview }})"></div>
                            @endif
                        @endforeach
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
