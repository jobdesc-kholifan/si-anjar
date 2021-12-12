<h4 class="pt-3 pb-2 px-2 mb-4 border-bottom">Form SK</h4>
<div class="form-group text-right">
    <button type="button" class="btn btn-outline-primary btn-sm" onclick="actionsSK.create()">
        <i class="fa fa-plus-circle"></i>
        <span class="ml-2">Pembaruan SK</span>
    </button>
</div>
<div class="form-group">
    <div class="w-100">
        <table class="table table-striped table-hover w-100" id="table-project-sk">
            <thead>
            <tr>
                <th data-data="checkbox" data-name="no" data-orderable="false" data-searchable="false"></th>
                <th data-data="no_ktp" data-name="no_ktp">Pembaruan Ke</th>
                <th data-data="investor_name" data-name="investor_name">No SK</th>
                <th data-data="investor_name" data-name="investor_name">Tanggal Cetak</th>
                <th data-data="action" data-orderable="false" data-searchable="false" style="width: 200px">Aksi</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<div class="footer-actions">
    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="$('#investor').trigger('click')">
        <i class="fa fa-angle-left"></i>
        <span class="ml-2">Sebelumnya</span>
    </button>
    <button type="button" class="btn btn-outline-primary btn-sm" onclick="$('#surkas').trigger('click')">
        <span class="mr-2">Selanjutnya</span>
        <i class="fa fa-angle-right"></i>
    </button>
</div>
