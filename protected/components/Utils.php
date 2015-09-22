<?php

abstract class Utils
{
    /**
     * 连接路径
     * @param string $a
     * @param string $b
     * @return string
     */
    public static function concatPath($a, $b){
        return implode(DIRECTORY_SEPARATOR, array_map(function($x){ return rtrim($x, "\\/"); }, func_get_args()));
    }

} 