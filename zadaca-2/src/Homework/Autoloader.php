<?php

namespace Sofa\Homework;

class Autoloader {
    public static function register()
    {
        spl_autoload_register(function ($class_path) {
            $namespace_prefix = 'Sofa\\Homework\\';
            $class_name = substr($class_path, strlen($namespace_prefix)); 

            $filePath = str_replace('\\', '/', $class_name);
            $filePath = __DIR__.'/'.$filePath.'.php';
        
            if (file_exists($filePath)) {
                require $filePath;
            }
        });
    }
}



