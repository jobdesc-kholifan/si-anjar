<div class="p-3">
    <div class="form-group">
        <label for="input-nama-darurat" class="required">Nama</label>
        <input
            type="text"
            id="input-nama-darurat"
            class="form-control"
            name="emergency_name"
            placeholder="{{ DBText::inputPlaceholder('Nama') }}"
            maxlength="100"
        />
    </div>
    <div class="form-group">
        <label for="input-no-hp" class="required">No HP</label>
        <input
            type="text"
            id="input-no-hp"
            class="form-control"
            name="emergency_phone_number"
            placeholder="{{ DBText::inputPlaceholder('No HP') }}"
            maxlength="100"
        />
    </div>
    <div class="form-group">
        <label for="input-hub-keluarga" class="required">Hub Keluarga</label>
        <input
            type="text"
            id="input-hub-keluarga"
            class="form-control"
            name="emergency_relationship"
            placeholder="{{ DBText::inputPlaceholder('Hub Keluarga') }}"
            maxlength="100"
        />
    </div>
</div>
<div class="footer-actions px-3">
    <button type="button" class="btn btn-outline-secondary btn-sm mr-2" onclick="$('#bank').trigger('click')">
        <i class="fa fa-angle-left"></i>
        <span class="ml-1">Kembali</span>
    </button>
    <button type="button" class="btn btn-outline-primary btn-sm" onclick="$('#lampiran').trigger('click')">
        <span class="mr-2">Selanjutnya</span>
        <i class="fa fa-angle-right"></i>
    </button>
</div>
