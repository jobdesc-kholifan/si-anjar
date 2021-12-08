<div class="p-3">
    <div class="form-group">
        <label for="input-name" class="required">Nama Investor</label>
        <input
            type="text"
            id="input-name"
            class="form-control"
            name="investor_name"
            placeholder="{{ DBText::inputPlaceholder('Nama Investor') }}"
            maxlength="100"
        />
    </div>
    <div class="form-group">
        <label for="input-email" class="required">Email</label>
        <input
            type="text"
            id="input-email"
            class="form-control"
            name="email"
            placeholder="{{ DBText::inputPlaceholder('Email') }}"
            maxlength="100"
        />
    </div>
    <div class="form-group">
        <label for="input-phone-number" class="required">No Handphone 1</label>
        <input
            type="text"
            id="input-phone-number"
            class="form-control"
            name="phone_number"
            placeholder="{{ DBText::inputPlaceholder('No Handphone 1') }}"
            maxlength="20"
        />
    </div>
    <div class="form-group">
        <label for="input-phone-number-alternative">No Handphone 2</label>
        <input
            type="text"
            id="input-phone-number-alternative"
            class="form-control"
            name="phone_number_alternative"
            placeholder="{{ DBText::inputPlaceholder('No Handphone 2') }}"
            maxlength="20"
        />
    </div>
    <div class="form-group">
        <label for="input-description">Alamat</label>
        <textarea
            id="input-description"
            class="form-control"
            name="address"
            placeholder="{{ DBText::inputPlaceholder('Alamat') }}"
            rows="5"
        ></textarea>
    </div>
</div>
<div class="footer-actions px-3">
    <button type="button" class="btn btn-outline-primary btn-sm" onclick="$('#pribadi').trigger('click')">
        <span class="mr-2">Selanjutnya</span>
        <i class="fa fa-angle-right"></i>
    </button>
</div>
