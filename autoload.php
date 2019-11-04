<?php

if (!function_exists('UPToolsAutoload')){
    function UPToolsAutoload($className)
    {
        if (strncmp('UPTools', $className, 7) === 0) {
            $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'library' .
                str_replace('\\', DIRECTORY_SEPARATOR, substr($className, 7)) . '.php';

            if (file_exists($path)) {
                require $path;
            }
        }
    }

    spl_autoload_register('UPToolsAutoload');
}
