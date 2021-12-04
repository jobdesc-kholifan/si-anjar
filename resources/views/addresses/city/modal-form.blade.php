<form>
    {{ csrf_field() }}
    <div class="modal-header">
        <h3 class="card-title">Form Bank</h3>
        <span class="close" data-dismiss="modal">&times;</span>
    </div>
    <div class="modal-body ">
        <div class="form-group">
            <label for="select-parent" class="required">Provinsi</label>
            <select
                id="select-parent"
                class="form-control"
                name="province_id"
                data-toggle="select2"
                data-url="{{ route(DBRoutes::addressesProvinceSelect) }}"
                required
            ></select>
        </div>
        <div class="form-group">
            <label for="input-name" class="required">Nama Kota / Kabupaten</label>
            <input
                type="text"
                id="input-name"
                class="form-control"
                name="city_name"
                placeholder="{{ DBText::inputPlaceholder('Nama Kota / Kabupaten') }}"
                maxlength="100"
                required
            />
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
