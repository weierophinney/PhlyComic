<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace PhlyComic\Console;

use PhlyComic\Comic;
use PhlyComic\ComicFactory;
use Symfony\Component\Console\Style\SymfonyStyle;

trait ComicConsoleTrait
{
    /**
     * Template used for comics
     *
     * @var string
     */
    private $comicTemplate = <<< 'EOT'
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
    private $errorTemplate = <<< 'EOT'
<div class="comic">
    <h4><a href="%s">%s</a></h4>
    <p class="error">%s</p>
</div>
EOT;

    private $status;

    /**
     * Report an error to the display
     *
     * @param SymfonyStyle $console
     * @param string $message
     * @param null|\Exception $e
     */
    private function reportError(SymfonyStyle $console, $message, $e = null)
    {
        $console->caution($message);
        if ($e) {
            $console->caution($e->getTraceAsString());
        }
    }

    /**
     * Fetch a named comic
     */
    private function fetchComic(string $name, SymfonyStyle $console) : ?Comic
    {
        $source  = ComicFactory::factory($name);
        $console->text(sprintf('<info>Fetching "%s"</>', $name));
        $console->progressStart();

        $comic = $source->fetch();

        $console->progressFinish();

        if (! $comic instanceof Comic) {
            $this->status = 1;
            $this->reportError($console, $source->getError());
            return false;
        }

        return $comic;
    }

    private function validateComicAlias(SymfonyStyle $console, string $alias) : bool
    {
        $comics = ComicFactory::getSupported();
        if (in_array($alias, array_keys($comics), true)) {
            return true;
        }

        $console->caution(sprintf("Comic '%s' is not supported", $alias));
        return false;
    }

    private function validateOutputValue(SymfonyStyle $console, string $output) : bool
    {
        if (! is_dir(dirname($output))) {
            $console->caution(sprintf("Output directory '%s' does not exist", dirname($output)));
            return false;
        }

        if (! is_writable(dirname($output))) {
            $console->caution(sprintf("Output directory '%s' is not writable", dirname($output)));
            return false;
        }

        return true;
    }
}
