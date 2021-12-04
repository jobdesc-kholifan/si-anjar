<form>
    {{ csrf_field() }}
    <div class="modal-header">
        <h3 class="card-title">Form Bank</h3>
        <span class="close" data-dismiss="modal">&times;</span>
    </div>
    <div class="modal-body ">
        <div class="form-group">
            <label for="input-name" class="required">Kode Bank</label>
            <input
                type="text"
                id="input-name"
                class="form-control"
                name="bank_code"
                placeholder="{{ DBText::inputPlaceholder('Kode Bank') }}"
                maxlength="15"
                required
            />
        </div>
        <div class="form-group">
            <label for="input-name" class="required">Nama Bank</label>
            <input
                type="text"
                id="input-name"
                class="form-control"
                name="bank_name"
                placeholder="{{ DBText::inputPlaceholder('Nama Bank') }}"
                maxlength="100"
                required
            />
        </div>
        <div class="form-group">
            <label for="input-description">Deskripsi</label>
            <textarea
                id="input-description"
                class="form-control"
                name="description"
                placeholder="{{ DBText::inputPlaceholder('Deskripsi') }}"
                rows="5"
            ></textarea>
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
