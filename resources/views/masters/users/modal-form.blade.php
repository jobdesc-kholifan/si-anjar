<?php

use App\Helpers\Collections\Config\ConfigArrayCollection;

/* @var ConfigArrayCollection $types */

?>
<form>
    {{ csrf_field() }}
    <div class="modal-header">
        <h3 class="card-title">Form {{ $title }}</h3>
        <span class="close" data-dismiss="modal">&times;</span>
    </div>
    <div class="modal-body p-0">
        <div class="p-3">
            <div class="form-group">
                <label for="input-fullname" class="required">Nama Lengkap</label>
                <input
                    type="text"
                    id="input-fullname"
                    class="form-control"
                    name="full_name"
                    placeholder="{{ DBText::inputPlaceholder('Nama Lengkap') }}"
                    maxlength="100"
                />
            </div>
            <div class="form-group">
                <label for="input-phone">No. Telp</label>
                <input
                    type="text"
                    id="input-phone"
                    class="form-control"
                    name="phone_number"
                    placeholder="{{ DBText::inputPlaceholder('No. Telp') }}"
                    maxlength="20"
                    onkeydown="return Helpers.isNumberKey(event)"
                />
            </div>
            <div class="form-group">
                <label for="input-email">Email</label>
                <input
                    type="email"
                    id="input-email"
                    class="form-control"
                    name="email"
                    placeholder="{{ DBText::inputPlaceholder('Email') }}"
                    maxlength="100"
                />
            </div>
            <div class="form-group">
                <label for="input-username" class="required">Nama Pengguna</label>
                <input
                    type="text"
                    id="input-username"
                    class="form-control"
                    name="user_name"
                    placeholder="{{ DBText::inputPlaceholder('Nama Pengguna') }}"
                    maxlength="30"
                />
            </div>
            <div class="form-group">
                <label for="input-password" class="required">Kata Sandi</label>
                <input
                    type="password"
                    id="input-password"
                    class="form-control"
                    name="user_password"
                    placeholder="{{ DBText::inputPlaceholder('Kata Sandi') }}"
                    maxlength="100"
                />
            </div>
            <div class="form-group">
                <label for="select-parent" class="required">Role</label>
                <select
                    id="select-parent"
                    class="form-control"
                    name="role_id"
                    data-toggle="select2"
                    data-url="{{ route(DBRoutes::mastersTypesSelect) }}"
                    data-params='{"parent_slug": ["{{ DBTypes::role }}"]}'
                    required
                ></select>
            </div>
            <div class="form-group">
                <label for="input-description">Keterangan</label>
                <textarea
                    id="input-description"
                    class="form-control"
                    name="description"
                    rows="5"
                    placeholder="{{ DBText::inputPlaceholder('Keterangan') }}"
                ></textarea>
            </div>
        </div>
        <div class="footer-actions px-3">
            <button type="button" class="btn btn-outline-danger btn-sm mr-1" data-dismiss="modal">
                <span>Batal</span>
            </button>
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <span>Simpan</span>
            </button>
        </div>
    </div>
</form>
