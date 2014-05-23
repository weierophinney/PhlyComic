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

abstract class AbstractConsoleHandler
{
    /**
     * Template used for comics
     * 
     * @var string
     */
    protected $comicTemplate =<<<EOT
<div class="comic">
    <h4><a href="%s">%s</a></h4>
    <p><a href="%s"><img src="%s"/></a></p>
</div>
EOT;

    /**
     * Template used for errors
     * 
     * @var string
     */
    protected $errorTemplate =<<<EOT
<div class="comic">
    <h4><a href="%s">%s</a></h4>
    <p class="error">%s</p>
</div>
EOT;

    /**
     * Console commands are invokables
     * 
     * @param Route $route 
     * @param Console $console 
     * @return int
     */
    abstract public function __invoke(Route $route, Console $console);

    /**
     * Report an error to the display
     *
     * @param Console $console 
     * @param int $width 
     * @param int $length 
     * @param string $message 
     * @param null|\Exception $e 
     */
    protected function reportError(Console $console, $width, $length, $message, $e = null)
    {
        if (($length + 9) > $width) {
            $console->writeLine('');
            $length = 0;
        }
        $spaces = $width - $length - 9;
        $console->writeLine(str_repeat('.', $spaces) . '[ ERROR ]', Color::RED);
        $console->writeLine($message);
        if ($e) {
            $console->writeLine($e->getTraceAsString());
        }
    }

    /**
     * Report a completion
     * 
     * @param Console $console 
     * @param int $width 
     * @param int $length 
     * @return void
     */
    protected function reportSuccess(Console $console, $width, $length)
    {
        if (($length + 8) > $width) {
            $console->writeLine('');
            $length = 0;
        }
        $spaces = $width - $length - 8;
        $console->writeLine(str_repeat('.', $spaces) . '[ DONE ]', Color::GREEN);
    }

    /**
     * Fetch a named comic
     * 
     * @param mixed $name 
     * @param Console $console 
     * @param int $width 
     * @return null|\PhlyComic\Comic
     */
    protected function fetchComic($name, Console $console, $width)
    {
        $source  = ComicFactory::factory($name);
        $console->write(sprintf(
            '%s "%s"',
            $console->colorize('Fetching', Color::BLUE),
            $name
        ));
        $message = sprintf('Fetching "%s"', $name);

        try {
            $comic  = $source->fetch();
        } catch (\Exception $e) {
            $error = sprintf(
                'Unable to fetch comic "%s"',
                $alias
            );
            $this->reportError($console, $width, strlen($message), $error, $e);
            return false;
        }

        if (! $comic instanceof Comic) {
            $this->reportError($console, $width, strlen($message), $source->getError());
            return false;
        }

        $this->reportSuccess($console, $width, strlen($message));

        return $comic;
    }
}
