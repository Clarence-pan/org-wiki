<?php
// register auto-loader with namespaces
call_user_func(function($namespaces){
    spl_autoload_register(function($class) use ($namespaces){
        $class = ltrim($class, '\\');
        $classLen = strlen($class);

        foreach ($namespaces as $nsName => $nsPath) {
            $nsLen = strlen($nsName);
            if ($classLen > $nsLen && strncmp($class, $nsName, $nsLen) === 0 && '\\' == $class[$nsLen]){
                $filePath = $nsPath . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, substr($class, $nsLen)).'.php';
                if (file_exists($filePath)){
                    include($filePath);
                }
            }
        }
    });
},[
    'html' => __DIR__.'/../components/html',
]);

