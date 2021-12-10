<h4 class="pt-3 pb-2 px-2 mb-4 border-bottom">Form Investor</h4>
<div class="form-group">
    <dl class="row">
        <dt class="col-4 col-sm-2">Proyek</dt>
        <dd class="col-8 col-sm-10">-</dd>
        <dt class="col-4 col-sm-2">Nilai Proyek</dt>
        <dd class="col-8 col-sm-10">-</dd>
        <dt class="col-4 col-sm-2">Modal Disetor</dt>
        <dd class="col-8 col-sm-10">-</dd>
        <dt class="col-4 col-sm-2">Kekurangan Modal Disetor</dt>
        <dd class="col-8 col-sm-10">-</dd>
    </dl>
</div>
<div class="form-group text-right">
    <button type="button" class="btn btn-outline-primary btn-sm" onclick="actionsInvestor.create()">
        <i class="fa fa-plus-circle"></i>
        <span class="ml-2">Tambah Investor</span>
    </button>
</div>
<div class="form-group">
    <div class="w-100">
        <table class="table table-striped table-hover" id="table-project-investor">
            <thead>
            <tr>
                <th data-data="checkbox" data-name="no" data-orderable="false" data-searchable="false"></th>
                <th data-data="no_ktp" data-name="no_ktp">NIK</th>
                <th data-data="investor_name" data-name="investor_name">Nama Investor</th>
                <th data-data="investor_name" data-name="investor_name">Porsi Saham</th>
                <th data-data="created_at" data-name="created_at">Nominal Disetor</th>
                <th data-data="action" data-orderable="false" data-searchable="false" style="width: 200px">Aksi</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<div class="footer-actions">
    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="$('#project').trigger('click')">
        <i class="fa fa-angle-left"></i>
        <span class="ml-2">Sebelumnya</span>
    </button>
    <button type="button" class="btn btn-outline-primary btn-sm" onclick="$('#sk').trigger('click')">
        <span class="mr-2">Selanjutnya</span>
        <i class="fa fa-angle-right"></i>
    </button>
</div>
