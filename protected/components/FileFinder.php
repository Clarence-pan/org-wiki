<?php

/**
 * Class FileFinder 文件查找器
 */
class FileFinder {
    /**
     * @param string     $path
     * @param array|null $params
     * @return Generator
     */
    public static function find($path, $params=null){
        $ignoreDir = $params['ignoreDir'];
        $fileExt = $params['fileExt'];

        foreach (scandir($path) as $file) {
            if ($file[0] === '.'){ // ignore all hidden dir/files (in linux)
                continue;
            }

            if (is_dir($file)){
                if (!empty($ignoreDir) && in_array($file, $ignoreDir)){
                    continue;
                } else {
                    foreach (self::find($path . DIRECTORY_SEPARATOR . $file, $params) as $innerFile) {
                        yield $file . DIRECTORY_SEPARATOR . $innerFile;
                    }
                }
            }

            if (!empty($fileExt) && !in_array(end(explode('.', $file)), (array)$fileExt)){
                continue;
            }

            yield $file;
        }
    }
}