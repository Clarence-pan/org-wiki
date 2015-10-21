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
     * create the dir if it does not exist
     * @param $dir string
     */
    public static function mkdirIfNotExists($dir){
        if (!is_dir($dir)){
            mkdir($dir, 0777, true);
        }
    }


    /**
     * normalize a path. i.e. convert './../test/../file' to '../file'
     * @param $path string
     * @return string
     */
    public static function normalizePath($path){
        // original: http://php.net/manual/en/function.realpath.php
        $parts = array();// Array to build a new path from the good parts
        $path = str_replace('\\', '/', $path);// Replace backslashes with forwardslashes
        $path = preg_replace('/\/+/', '/', $path);// Combine multiple slashes into a single slash
        $segments = explode('/', $path);// Collect path segments

        $test = '';// Initialize testing variable
        foreach($segments as $segment)
        {
            if($segment != '.')
            {
                $test = array_pop($parts);
                if(is_null($test))
                    $parts[] = $segment;
                else if($segment == '..')
                {
                    if($test == '..')
                        $parts[] = $test;

                    if($test == '..' || $test == '')
                        $parts[] = $segment;
                }
                else
                {
                    $parts[] = $test;
                    $parts[] = $segment;
                }
            }
        }

        return implode('/', $parts);
    }


} 