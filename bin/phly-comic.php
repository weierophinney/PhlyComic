#!/usr/bin/env php
<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

use Zend\Console\Console;
use ZF\Console\Application;
use ZF\Console\Dispatcher;

switch (true) {
    case (file_exists(__DIR__ . '/../vendor/autoload.php')):
        // Installed standalone
        require __DIR__ . '/../vendor/autoload.php';
        break;
    case (file_exists(__DIR__ . '/../../../autoload.php')):
        // Installed as a Composer dependency
        require __DIR__ . '/../../../autoload.php';
        break;
    case (file_exists('vendor/autoload.php')):
        // As a Composer dependency, relative to CWD
        require 'vendor/autoload.php';
        break;
    default:
        throw new RuntimeException('Unable to locate Composer autoloader; please run "composer install".');
}

define('VERSION', '1.0.0-dev');

$dispatcher  = new Dispatcher();
$dispatcher->map('fetch-all', 'PhlyComic\Console\FetchAllComics');

$application = new Application(
    'PhlyComic',
    VERSION,
    include __DIR__ . '/../config/routes.php',
    Console::getInstance(),
    $dispatcher
);
$exit = $application->run();
exit($exit);
