<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace PhlyComic\Console;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use ZF\Console\Route;

class FetchComic extends AbstractConsoleHandler
{
    public function __invoke(Route $route, Console $console)
    {
        $width = $console->getWidth();
        $name  = $route->getMatchedParam('comic');

        $comic = $this->fetchComic($name, $console, $width);
        if (! $comic) {
            return 1;
        }

        $message = 'Generating HTML';
        $console->write($message, Color::BLUE);

        if ($comic->hasError()) {
            $html = sprintf($this->errorTemplate . "\n", $comic->getLink(), $comic->getName(), $comic->getError());
        } else {
            $html = sprintf($this->comicTemplate . "\n", $comic->getLink(), $comic->getName(), $comic->getDaily(), $comic->getImage());
        }

        $path = $this->getComicPath($name, $route);
        file_put_contents($path, $html);
        $this->reportSuccess($console, $width, strlen($message));
        $console->writeLine(sprintf(
            '%s %s',
            $console->colorize('Comic written to ', Color::GREEN),
            $path
        ));

        return 0;
    }

    /**
     * Get path to which to write comic HTML
     * 
     * @param string $name 
     * @param Route $route 
     * @return string
     */
    protected function getComicPath($name, Route $route)
    {
        $path = $route->getMatchedParam('output', false);
        if (! $path) {
            $path = sprintf('data/comics/%s.html', $name);
        }
        return $path;
    }
}
