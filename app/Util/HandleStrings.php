<?php
/**
 * Created by PhpStorm.
 * User: gil
 * Date: 5/7/18
 * Time: 2:11 PM
 */

namespace App\Util;


class HandleStrings
{
    /**
     * Remove accents of string
     * @param $string
     * @return null|string|string[]
     */
    public static function removeAccents($string){
        return preg_replace(array("/(ç)/", "/(Ç)/", "/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/",
            "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"),
            explode(" ","c C a A e E i I o O u U n N"), $string);
    }

    public static function removeSpace($string){
        return preg_replace('/\s+/', '', $string);
    }
}