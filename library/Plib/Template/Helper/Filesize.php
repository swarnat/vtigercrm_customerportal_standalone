<?php
class Plib_Template_Helper_Filesize
{
    public static function filesize($template, $value) {
        if($value > 1024 * 1024 * 1024 * 1024) {
            return round($value / (1024 * 1024 * 1024 * 1024), 2)." TB";
        }
        if($value > 1024 * 1024 * 1024) {
            return round($value / (1024 * 1024 * 1024), 2)." GB";
        }
        if($value > 1024 * 1024) {
            return round($value / (1024 * 1024),2)." MB";
        }
        if($value > 10244) {
            return round($value / (1024),2)." KB";
        }

        return $value." Bytes";

    }
}
