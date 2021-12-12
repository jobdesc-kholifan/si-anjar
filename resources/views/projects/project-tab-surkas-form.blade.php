<form>
    {{ csrf_field() }}
    <div class="modal-header">
        <h3 class="card-title">Form Surkas</h3>
        <span class="close" data-dismiss="modal">&times;</span>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label for="input-porsi">Nama Proyek: -</label>
        </div>
        <div class="form-group">
            <label for="input-nominal" class="required">Jumlah Surkas</label>
            <input
                type="text"
                id="input-nominal"
                class="form-control"
                placeholder="Rp. 0"
            />
        </div>
        <div class="form-group">
            <label for="input-surkas-date" class="required">Tanggal Surkas</label>
            <input
                type="text"
                id="input-surkas-date"
                class="form-control"
                name="finish_date"
                placeholder="{{ DBText::datePlaceholder() }}"
                data-toggle="daterangepicker"
                data-format="DD/MM/YYYY"
                data-single-date="true"
                data-auto-apply="true"
                data-show-dropdowns="true"
            />
        </div>
        <div class="form-group">
            <label for="input-porsi">Lampiran</label>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-danger btn-sm" data-dismiss="modal">
            <span>Batal</span>
        </button>
        <button type="submit" class="btn btn-outline-primary btn-sm">
            <span>Simpan</span>
        </button>
    </div>
</form>
