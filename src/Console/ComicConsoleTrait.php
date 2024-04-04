<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace PhlyComic\Console;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use PhlyComic\Comic;
use PhlyComic\ComicFactory;
use PhlyComic\HttpClient;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

trait ComicConsoleTrait
{
    private ?HttpClient $client = null;
    private ?ComicFactory $factory = null;

    /**
     * Template used for comics
     *
     * @var string
     */
    private $comicTemplate = <<< 'EOT'
        <div class="comic">
            <h4><a href="%s">%s</a></h4>
            <p><a href="%s"><img referrerpolicy="no-referrer" src="%s"/></a></p>
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
        $source  = $this->getFactory()->get($name);
        $console->text(sprintf('<info>Fetching "%s"</>', $name));
        $console->progressStart();

        $comic = $source->fetch($this->getHttpClient());

        $console->progressFinish();

        if ($comic->hasError()) {
            $this->status = 1;
            $this->reportError($console, $comic->error);
            return null;
        }

        return $comic;
    }

    private function validateComicAlias(SymfonyStyle $console, string $alias) : bool
    {
        if ($this->getFactory()->has($alias)) {
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

    private function getFactory(): ComicFactory
    {
        if (null === $this->factory) {
            $this->factory = new ComicFactory();
        }

        return $this->factory;
    }

    private function getHttpClient(): HttpClient
    {
        if (null === $this->client) {
            $this->client = new HttpClient(
                Psr18ClientDiscovery::find(),
                Psr17FactoryDiscovery::findRequestFactory(),
            );
        }

        return $this->client;
    }
}
