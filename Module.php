<?php

namespace PhlyComic;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;

/**
 * This is a simple ZF2 module.
 *
 * This library can be integrated into a ZF2 application via this Module class. 
 * However, it does not actually serve any MVC artifacts.
 */
class Module implements ConsoleUsageProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getControllerConfig()
    {
        return array('factories' => array(
            'PhlyComic\Fetch' => function ($controllers) {
                $services = $controllers->getServiceLocator();
                $config   = $services->get('Config');
                $config   = $config['phly-comic'];

                $controller = new FetchController();
                $controller->setConsole($services->get('Console'));
                $controller->setConfig($config);
                return $controller;
            },
        ));
    }

    public function getConsoleUsage(Console $console)
    {
        return array(
            'phlycomic fetch all' => 'Fetch all comics and cache to a view script',
            'phlycomic list' => 'List available comics',
            'phlycomic fetch one --name' => 'Fetch a named comic',
        );
    }
}
