<form>
    {{ csrf_field() }}
    <div class="modal-header">
        <h3 class="card-title">Form {{ $title }}</h3>
        <span class="close" data-dismiss="modal">&times;</span>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label for="input-title" class="required">Nama Fitur</label>
            <input
                type="text"
                id="input-title"
                class="form-control"
                name="title"
                placeholder="{{ DBText::inputPlaceholder('Nama Fitur') }}"
                maxlength="100"
                required
            />
        </div>
        <div class="form-group">
            <label for="input-slug" class="required">Slug</label>
            <input
                type="text"
                id="input-slug"
                class="form-control"
                name="slug"
                placeholder="{{ DBText::inputPlaceholder("Slug") }}"
                required
            />
        </div>
        <div class="form-group">
            <label for="input-description">Deskripsi</label>
            <textarea
                id="input-description"
                class="form-control"
                name="description"
                rows="5"
                placeholder="{{ DBText::inputPlaceholder('Deskripsi') }}"
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
