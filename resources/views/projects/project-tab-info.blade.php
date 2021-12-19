<?php

$hasUpdate = findPermission(DBMenus::project)->hasAccess(DBFeature::update);

?>
<h4 class="pt-3 pb-2 px-2 border-bottom">Form Proyek</h4>
<div class="pt-3">
    <div class="form-group">
        <div class="row justify-content-center align-items-center">
            <label for="input-project-name" class="col-12 col-sm-2 text-left text-sm-right required">Nama Project</label>
            <div class="col-12 col-sm-10">
                <input
                    type="text"
                    id="input-project-name"
                    class="form-control"
                    name="project_name"
                    placeholder="{{ DBText::inputPlaceholder('Nama Project') }}"
                    maxlength="100"
                />
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row justify-content-center align-items-center">
            <label for="select-category" class="col-12 col-sm-2 text-left text-sm-right required">Kategori</label>
            <div class="col-12 col-sm-10">
                <select
                    id="select-category"
                    class="form-control"
                    name="project_category_id"
                    data-toggle="select2"
                    data-url="{{ route(DBRoutes::mastersTypesSelect) }}"
                    data-params='{"parent_slug": ["{{ DBTypes::categoryProject }}"]}'
                ></select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row justify-content-center align-items-center">
            <label for="input-value" class="col-12 col-sm-2 text-left text-sm-right required">Nilai Project</label>
            <div class="col-12 col-sm-10">
                <input
                    type="text"
                    id="input-value"
                    class="form-control"
                    name="project_value"
                    data-toggle="jquery-number"
                    placeholder="{{ DBText::inputPlaceholder('Nilai Project') }}"
                    maxlength="100"
                />
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row justify-content-center align-items-center">
            <label for="input-shares" class="col-12 col-sm-2 text-left text-sm-right required">Lembar Saham</label>
            <div class="col-12 col-sm-10">
                <input
                    type="text"
                    id="input-shares"
                    class="form-control"
                    name="project_shares"
                    data-toggle="jquery-number"
                    placeholder="{{ DBText::inputPlaceholder('Lembar Saham') }}"
                    maxlength="100"
                />
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row justify-content-center align-items-center">
            <label for="input-start-date" class="col-12 col-sm-2 text-left text-sm-right required">Tanggal Mulai</label>
            <div class="col-12 col-sm-10">
                <input
                    type="text"
                    id="input-start-date"
                    class="form-control"
                    name="start_date"
                    placeholder="{{ DBText::datePlaceholder() }}"
                    data-toggle="daterangepicker"
                    data-format="DD/MM/YYYY"
                    data-single-date="true"
                    data-auto-apply="true"
                    data-show-dropdowns="true"
                    data-disabled-next-date="false"
                />
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row justify-content-center align-items-center">
            <label for="input-finish-date" class="col-12 col-sm-2 text-left text-sm-right required">Tanggal Selesai</label>
            <div class="col-12 col-sm-10">
                <input
                    type="text"
                    id="input-finish-date"
                    class="form-control"
                    name="finish_date"
                    placeholder="{{ DBText::datePlaceholder() }}"
                    data-toggle="daterangepicker"
                    data-format="DD/MM/YYYY"
                    data-single-date="true"
                    data-auto-apply="true"
                    data-show-dropdowns="true"
                    data-disabled-next-date="false"
                />
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row justify-content-center align-items-center">
            <label for="input-estimane-profit" class="col-12 col-sm-2 text-left text-sm-right required">Proyeksi Keuntungan</label>
            <div class="col-12 col-sm-10">
                <div class="row">
                    <div class="col-4">
                        <input
                            type="text"
                            id="input-project-name"
                            class="form-control"
                            name="estimate_profit_value"
                            placeholder="0"
                            data-toggle="jquery-number"
                            maxlength="100"
                        />
                    </div>
                    <div class="col-8">
                        <label for="select-profit-type" class="d-none"></label>
                        <select
                            id="select-profit-type"
                            class="form-control"
                            name="estimate_profit_id"
                            data-toggle="select2"
                            data-url="{{ route(DBRoutes::mastersTypesSelect) }}"
                            data-params='{"parent_slug": ["{{ DBTypes::projectValue }}"]}'
                        ></select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row justify-content-center align-items-start">
            <label for="file-proposal" class="col-12 col-sm-2 text-left text-sm-right mt-2 required">Proposal Proyek</label>
            <div class="col-12 col-sm-10" id="file-proposal"></div>
        </div>
    </div>
    <div class="form-group">
        <div class="row justify-content-center align-items-start">
            <label for="file-bukti-transfer" class="col-12 col-sm-2 text-left text-sm-right mt-2 required">Bukti Transfer Proyek</label>
            <div class="col-12 col-sm-10" id="file-bukti-transfer"></div>
        </div>
    </div>
    <div class="form-group">
        <div class="row justify-content-center align-items-start">
            <label for="file-lampiran" class="col-12 col-sm-2 text-left text-sm-right required">Lampiran</label>
            <div class="col-12 col-sm-10" id="file-lampiran-project"></div>
        </div>
    </div>
</div>
<div class="footer-actions">
    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="$('#pic').trigger('click')">
        <i class="fa fa-angle-left"></i>
        <span class="ml-1">Sebelumnya</span>
    </button>
    @if(!$saveNext && $hasUpdate)
        <button type="submit" class="btn btn-primary btn-sm">
            <span>Simpan</span>
        </button>
    @endif
    @if($saveNext)
        <button type="submit" class="btn btn-outline-primary btn-sm">
            <span class="mr-2">Selanjutnya</span>
            <i class="fa fa-angle-right"></i>
        </button>
    @else
        <a href="{{ route(DBRoutes::projectInvestor, [$projectId]) }}" onclick="return confirm('Pastikan anda sudah klik tombol simpan, jika tidak data tidak akan tersimpan. Apakah anda yakin ingin lanjut input data investor?')" class="btn btn-outline-primary btn-sm">
            <span class="mr-1">Selanjutnya</span>
            <i class="fa fa-angle-right"></i>
        </a>
    @endif
</div>
