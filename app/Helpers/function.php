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

    function dbDate($date) {
        $newDate = str_replace("/", "-", $date);
        return date('Y-m-d', strtotime($newDate));
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
        return DB::raw(sprintf("CONCAT('%s/',REPLACE(directory, '/', '_'), '/%s/show/', file_name) as %s", url('preview'), env('APP_KEY_VALUE'), $alias));
    }
}
