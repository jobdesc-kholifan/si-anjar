<form>
    {{ csrf_field() }}
    <div class="modal-header">
        <h3 class="card-title">Form SK</h3>
        <span class="close" data-dismiss="modal">&times;</span>
    </div>
    <div class="modal-body">
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
