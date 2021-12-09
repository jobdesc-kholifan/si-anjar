<form>
    {{ csrf_field() }}
    <div class="modal-header">
        <h3 class="card-title">Form Investor</h3>
        <span class="close" data-dismiss="modal">&times;</span>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label for="select-investor" class="required">Nama Investor</label>
            <select
                id="select-investor"
                class="form-control"
                data-toggle="select2"
            ></select>
        </div>
        <div class="form-group">
            <label for="input-nominal" class="required">Nominal</label>
            <input
                type="text"
                id="input-nominal"
                class="form-control"
                placeholder="Rp. 0"
            />
        </div>
        <div class="form-group">
            <label for="input-porsi">Porsi Saham: 0%</label>
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
