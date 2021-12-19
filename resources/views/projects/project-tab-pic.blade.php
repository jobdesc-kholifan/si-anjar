<?php

$hasUpdate = findPermission(DBMenus::project)->hasAccess(DBFeature::update);

?>
<h4 class="pt-3 pb-2 px-2 border-bottom">Form PIC</h4>
<div id="form-pic"></div>
<div class="footer-actions">
    @if(!$saveNext && $hasUpdate)
        <button type="submit" class="btn btn-primary btn-sm">
            <span>Simpan</span>
        </button>
    @endif
    <button type="button" class="btn btn-outline-primary btn-sm" onclick="$('#project').trigger('click')">
        <span class="mr-2">Selanjutnya</span>
        <i class="fa fa-angle-right"></i>
    </button>
</div>
