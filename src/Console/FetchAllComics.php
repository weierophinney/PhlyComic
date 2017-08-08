<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace PhlyComic\Console;

use PhlyComic\Comic;
use PhlyComic\ComicFactory;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use ZF\Console\Route;

class FetchAllComics extends AbstractConsoleHandler
{
    /**
     * Fetch all comics and write to a file
     *
     * @param Route $route
     * @param Console $console
     * @return int
     */
    public function __invoke(Route $route, Console $console)
    {
        $supported = ComicFactory::getSupported();
        ksort($supported);
        $supported = array_keys($supported);

        $exclude = $route->getMatchedParam('exclude', []);
        $toFetch = array_filter($supported, function ($comic) use ($exclude) {
            return ! in_array($comic, $exclude);
        });

        $width = $console->getWidth();
        $html  = '';

        foreach ($toFetch as $alias) {
            $comic = $this->fetchComic($alias, $console, $width);
            if (! $comic instanceof Comic) {
                continue;
            }

            if ($comic->hasError()) {
                $html .= sprintf($this->errorTemplate . "\n", $comic->getLink(), $comic->getName(), $comic->getError());
                continue;
            }

            $html .= sprintf($this->comicTemplate . "\n", $comic->getLink(), $comic->getName(), $comic->getDaily(), $comic->getImage());
        }

        $path = $route->getMatchedParam('output');
        file_put_contents($path, $html);
        $console->writeLine(sprintf(
            '%s %s',
            $console->colorize('Comics written to', Color::GREEN),
            $path
        ));
        return 0;
    }
}
