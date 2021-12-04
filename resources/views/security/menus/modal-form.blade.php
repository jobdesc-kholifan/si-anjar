<form>
    {{ csrf_field() }}
    <div class="modal-header">
        <h3 class="card-title">Form {{ $title }}</h3>
        <span class="close" data-dismiss="modal">&times;</span>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label for="select-parent">Parent</label>
            <select
                id="select-parent"
                class="form-control"
                name="parent_id"
                data-toggle="select2"
                data-url="{{ route(DBRoutes::securityMenuSelect) }}"
            ></select>
        </div>
        <div class="form-group">
            <label for="input-name" class="required">Nama Menu</label>
            <input
                type="text"
                id="input-name"
                class="form-control"
                name="name"
                placeholder="{{ DBText::inputPlaceholder('Nama Menu') }}"
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
            <label for="input-icon">Icon</label>
            <input
                type="text"
                id="input-icon"
                class="form-control"
                name="icon"
                placeholder="{{ DBText::inputPlaceholder("Icon") }}"
            />
        </div>
        <div class="form-group">
            <label for="input-sequence">Urutan</label>
            <input
                id="input-sequence"
                class="form-control"
                name="sequence"
                placeholder="0"
                onkeydown="return Helpers.isNumberKey(event)"
                maxlength="5"
            />
        </div>
        <div class="form-group">
            <label for="input-sequence">Template Fitur</label>
            <div class="form-check">
                <input type="checkbox" name="withcrud" class="form-check-input" id="crud">
                <label class="form-check-label" for="crud">Fitur CRUD</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="withexcel" class="form-check-input" id="import-export-excel">
                <label class="form-check-label" for="import-export-excel">Fitur Import Export Excel</label>
            </div>
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
