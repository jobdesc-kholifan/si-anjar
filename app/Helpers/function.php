<?php

use App\Helpers\Finders\FindConfig;
use App\Helpers\Finders\FindPermission;
use Illuminate\Support\Facades\DB;

if(!function_exists('isMenuActive')) {

    function isMenuActive($currentRoute, $route) {
        return in_array($route, $currentRoute) ? ' active' : '';
    }
}

if(!function_exists('isParentMenuActive')) {

    function isParentMenuActive($route, array $arrayRotues = array()) {
        return in_array($route, $arrayRotues) ? ' active' : '';
    }
}

if(!function_exists('isParentMenuOpen')) {

    function isParentMenuOpen($route, array $arrayRotues = array()) {
        return in_array($route, $arrayRotues) ? ' menu-open' : '';
    }
}

if(!function_exists('currentDate')) {

    function currentDate($format = 'Y-m-d H:i:s') {
        return date($format);
    }
}

if(!function_exists('dbDate')) {

    function dbDate($date, $format = 'Y-m-d') {
        if(!empty($date)) {
            $newDate = str_replace("/", "-", $date);
            return date($format, strtotime($newDate));
        }

        return date($format);
    }
}

if(!function_exists('dbIDR')) {

    function dbIDR($value) {
        $nominal = str_replace(",", "", $value);
        return str_replace(".", "", $nominal);
    }
}

if(!function_exists('IDR')) {

    function IDR($value, $symbol = 'Rp. ', $decimal = 0, $dec_point = ',', $thousands_sep = '.') {
        $nominal = number_format($value, $decimal, $dec_point, $thousands_sep);
        return $symbol.$nominal;
    }
}

if(!function_exists('IDRLabel')) {
    function IDRLabel($value, $symbol = 'Rp. ', $decimal = 0, $dec_point = ',', $thousands_sep = '.') {
        return "<div class=\"text-right w-100\">".IDR($value, $symbol, $decimal, $dec_point, $thousands_sep)."</div>";
    }
}

if(!function_exists('findConfig')) {

    /**
     * @param string $key
     *
     * @throws Exception
     * @return FindConfig
     * */
    function findConfig($key = 'slug') {
        $finder = new FindConfig();
        $finder->setKey($key);

        return $finder;
    }
}

if(!function_exists('findPermission')) {

    function findPermission($slugMenu) {
        return new FindPermission($slugMenu);
    }
}

if (!function_exists('toSlug')) {

    function toSlug($value, $replacement = '', $pattern = '/\s+/') {
        $slug = trim(strtolower(preg_replace($pattern, $replacement, $value)));
        return sprintf("usr-%s", substr($slug, 0, 100));
    }
}

if (!function_exists('fileUnlink')) {

    function fileUnlink($files) {
        foreach($files as $file) {
            if(!empty($file->directory) && !empty($file->file_name)) {
                $path = storage_path($file->directory) . DIRECTORY_SEPARATOR .$file->file_name;
                if(file_exists($path))
                    unlink($path);
            }
        }
    }
}

if (!function_exists('DBImage')) {

    function DBImage($alias = 'preview') {
        return DB::raw(sprintf("CONCAT('%s/',REPLACE(directory, '/', '_'), '/show/', file_name) as %s", url('preview'), $alias));
    }
}

if(!function_exists('configKey')) {

    function configKey($value) {
        $slug = trim(strtolower(preg_replace('/\s+/', '', $value)));
        return sprintf("usr-%s", $slug);
    }
}

function hex2dec($color = "#000000"){
    $tbl_color = array();
    $tbl_color['R']=hexdec(substr($color, 1, 2));
    $tbl_color['G']=hexdec(substr($color, 3, 2));
    $tbl_color['B']=hexdec(substr($color, 5, 2));
    return $tbl_color;
}

function px2mm($px){
    return $px*25.4/72;
}

function txtentities($html){
    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);
    return strtr($html, $trans);
}
