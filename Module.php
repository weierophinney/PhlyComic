<?php

namespace PhlyComic;

use Zend\Module\Consumer\AutoloaderProvider;

/**
 * This is a simple ZF2 module.
 *
 * This library can be integrated into a ZF2 application via this Module class. 
 * However, it does not actually serve any MVC artifacts.
 */
class Module implements AutoloaderProvider
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            ),
        );
    }
}
