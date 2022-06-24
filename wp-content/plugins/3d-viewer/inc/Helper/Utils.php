<?php
namespace BP3D\Helper;

class Utils {

    public static function isset($array, $key, $default = false){
        if(isset($array[$key])){
            return $array[$key];
        }
        return $default;
    }
}