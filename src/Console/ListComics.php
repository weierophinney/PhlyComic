<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace PhlyComic\Console;

use PhlyComic\ComicFactory;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use ZF\Console\Route;

class ListComics
{
    public function __invoke(Route $route, Console $console)
    {
        $comics = ComicFactory::getSupported();
        ksort($comics);

        $mapped = array_map(function ($name) {
            return strlen($name);
        }, array_keys($comics));
        $longest = array_reduce($mapped, function ($count, $longest) {
            $longest = ($count > $longest) ? $count : $longest;
            return $longest;
        }, 0);

        $console->writeLine('Supported comics:', Color::GREEN);
        foreach ($comics as $alias => $info) {
            $console->writeLine(sprintf(
                "    %s: %s%s",
                $console->colorize($alias, Color::BLUE),
                str_repeat(' ', $longest - strlen($alias)),
                $info['name']
            ));
        }
    }
}
