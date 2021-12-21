<?php

use App\Helpers\Collections\Config\ConfigCollection;

/**
 * @var ConfigCollection[] $genders
 * */

?>
<div class="p-3">
    <div class="form-group">
        <label for="input-no-ktp">No. KTP</label>
        <input
            type="text"
            id="input-no-ktp"
            class="form-control"
            name="no_ktp"
            placeholder="{{ DBText::inputPlaceholder('No. KTP') }}"
            maxlength="100"
        />
    </div>
    <div class="form-group">
        <label for="input-npwp">NPWP</label>
        <input
            type="text"
            id="input-npwp"
            class="form-control"
            name="npwp"
            placeholder="{{ DBText::inputPlaceholder('NPWP') }}"
            maxlength="100"
        />
    </div>
    <div class="form-group">
        <label for="input-pob" class="required">Tempat Tanggal Lahir</label>
        <div class="row">
            <div class="col-sm-8">
                <input
                    type="text"
                    id="input-pob"
                    class="form-control"
                    name="place_of_birth"
                    placeholder="{{ DBText::inputPlaceholder('Tempat Lahir') }}"
                    maxlength="100"
                />
            </div>
            <div class="col-sm-4">
                <label for="input-dob" class="d-none"></label>
                <input
                    type="text"
                    id="input-dob"
                    class="form-control"
                    name="date_of_birth"
                    placeholder="{{ DBText::datePlaceholder() }}"
                    data-toggle="daterangepicker"
                    data-format="DD/MM/YYYY"
                    data-single-date="true"
                    data-auto-apply="true"
                    data-show-dropdowns="true"
                />
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="select-religion" class="required">Jenis Kelamin</label>
        <div class="d-flex">
            @foreach($genders as $gender)
                <div class="form-check ml-1">
                    <input class="form-check-input" id="{{ $gender->getSlug() }}" type="radio" name="gender_id" value="{{ $gender->getId() }}">
                    <label class="form-check-label" for="{{ $gender->getSlug() }}">{{ $gender->getName() }}</label>
                </div>
            @endforeach
        </div>
    </div>
    <div class="form-group">
        <label for="select-religion" class="required">Agama</label>
        <select
            id="select-religion"
            class="form-control"
            name="religion_id"
            data-toggle="select2"
            data-url="{{ route(DBRoutes::mastersTypesSelect) }}"
            data-params='{"parent_slug": ["{{ DBTypes::religion }}"]}'
        ></select>
    </div>
    <div class="form-group">
        <label for="select-relationship" class="required">Status Perkawinan</label>
        <select
            id="select-relationship"
            class="form-control"
            name="relationship_id"
            data-toggle="select2"
            data-url="{{ route(DBRoutes::mastersTypesSelect) }}"
            data-params='{"parent_slug": ["{{ DBTypes::relationship }}"]}'
        ></select>
    </div>
    <div class="form-group">
        <label for="input-job-name" class="required">Pekerjaan</label>
        <input
            type="text"
            id="input-job-name"
            class="form-control"
            name="job_name"
            placeholder="{{ DBText::inputPlaceholder('Pekerjaan') }}"
            maxlength="100"
        />
    </div>
</div>
<div class="footer-actions px-3">
    <button type="button" class="btn btn-outline-secondary btn-sm mr-2" onclick="$('#kotak').trigger('click')">
        <i class="fa fa-angle-left"></i>
        <span class="ml-1">Kembali</span>
    </button>
    <button type="button" class="btn btn-outline-primary btn-sm" onclick="$('#bank').trigger('click')">
        <span class="mr-2">Selanjutnya</span>
        <i class="fa fa-angle-right"></i>
    </button>
</div>
