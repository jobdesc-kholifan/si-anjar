<?php

use App\Helpers\Collections\Projects\ProjectCollection;

/* @var ProjectCollection $project */

?>
<form>
    {{ csrf_field() }}
    <div class="modal-header">
        <h3 class="card-title">Form Surkas</h3>
        <span class="close" data-dismiss="modal">&times;</span>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label for="input-porsi">Nama Proyek: {{ $project->getName() }}</label>
        </div>
        <div class="form-group">
            <label for="input-surkas" class="required">No Surkas</label>
            <input
                type="text"
                id="input-surkas"
                class="form-control"
                name="surkas_no"
                placeholder="{{ DBText::inputPlaceholder('No Surkas') }}"
                required
                value="{{ $noSurkas }}"
            />
        </div>
        <div class="form-group">
            <label for="input-nominal" class="required">Jumlah Surkas</label>
            <input
                type="text"
                id="input-nominal"
                class="form-control"
                name="surkas_value"
                data-toggle="jquery-number"
                placeholder="Rp. 0"
                maxlength="100"
            />
        </div>
        @if(findPermission(DBMenus::project)->hasAccess(DBFeature::surkasAdminFee))
        <div class="form-group">
            <label for="input-admin-fee" class="required">Biaya Admin</label>
            <input
                type="text"
                id="input-admin-fee"
                class="form-control"
                name="admin_fee"
                data-toggle="jquery-number"
                placeholder="Rp. 0"
                maxlength="100"
            />
        </div>
        @endif
        <div class="form-group">
            <label for="input-surkas-date" class="required">Tanggal Surkas</label>
            <input
                type="text"
                id="input-surkas-date"
                class="form-control"
                name="surkas_date"
                placeholder="{{ DBText::datePlaceholder() }}"
                data-toggle="daterangepicker"
                data-format="DD/MM/YYYY"
                data-single-date="true"
                data-auto-apply="true"
                data-show-dropdowns="true"
            />
        </div>
        <div class="form-group">
            <label for="input-description">Berita Transfer (opsional)</label>
            <textarea
                id="input-description"
                class="form-control"
                name="description"
                placeholder="{{ DBText::inputPlaceholder('Berita Transfer') }}"
                rows="5"
            ></textarea>
        </div>
        <div class="form-group">
            <label for="input-other-description">Berita Transfer Tambahan (opsional)</label>
            <textarea
                id="input-other-description"
                class="form-control"
                name="other_description"
                placeholder="{{ DBText::inputPlaceholder('Berita Transfer Tambahan') }}"
                rows="5"
            ></textarea>
        </div>
        <div class="form-group">
            <label for="file-lampiran-surkas" class="col-12 col-sm-2 text-left text-sm-right required">Lampiran</label>
            <div class="col-12 col-sm-10" id="file-lampiran-surkas"></div>
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
