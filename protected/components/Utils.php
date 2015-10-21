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

    /**
     * 获取文件的扩展名（无'.')
     * @param $file
     * @return string
     */
    public static function getFileExt($file){
        if (preg_match('/\.(?<ext>[^.]*)$/', $file, $matches)){
            return $matches['ext'];
        }
        return '';
    }


    /**
     * @param $dir string
     */
    public static function mkdirIfNotExists($dir){
        if (!is_dir($dir)){
            mkdir($dir, 0777, true);
        }
    }

} 