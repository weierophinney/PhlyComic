<?php
namespace PhlyComic;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Controller\AbstractActionController;

class FetchController extends AbstractActionController
{
    protected $config = array();
    protected $console;

    public function setConsole(Console $console)
    {
        $this->console = $console;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getAllComicsPath()
    {
        $path  = rtrim($this->config['output_path'], '\\/');
        $path .= DIRECTORY_SEPARATOR . $this->config['all_comics_file'];
        return $path;
    }

    public function getComicPath($name)
    {
        $path  = rtrim($this->config['output_path'], '\\/');
        $path .= DIRECTORY_SEPARATOR . sprintf($this->config['comic_file'], $name);
        return $path;
    }

    public function oneAction()
    {
        $this->verifyConsole();

        $width = $this->console->getWidth();
        $name  = $this->params()->fromRoute('name', false);
        if (!$name) {
            $this->reportError($width, 0, 'No name was provided; unable to fetch comic');
            return;
        }

        $comics = ComicFactory::getSupported();
        ksort($comics);

        if (!in_array($name, array_keys($comics))) {
            $message = sprintf('Comic "%s" is unsupported; please use "phlycomic list" to find supported comics', $name);
            $this->reportError($width, 0, $message);
            return;
        }

        $message = 'Fetching comic "' . $name . '"';
        $this->console->write($message, Color::BLUE);
        $source = ComicFactory::factory($name);
        try {
            $comic  = $source->fetch();
        } catch (Exception $e) {
            $error = 'Unable to fetch comic';
            $this->reportError($width, strlen($message), $error, $e);
            return;
        }
        if (!$comic) {
            $this->reportError($width, strlen($message), $source->getError());
            return;
        }
        $this->reportSuccess($width, strlen($message));

        $message = 'Generating HTML';
        $this->console->write($message, Color::BLUE);

        $template =<<<EOT
<div class="comic">
    <h4><a href="%s">%s</a></h4>
    <p><a href="%s"><img src="%s"/></a></p>
</div>
EOT;

        $errTemplate =<<<EOT
<div class="comic">
    <h4><a href="%s">%s</a></h4>
    <p class="error">%s</p>
</div>
EOT;

        if ($comic->hasError()) {
            $html = sprintf($errTemplate . "\n", $comic->getLink(), $comic->getName(), $comic->getError());
        } else {
            $html = sprintf($template . "\n", $comic->getLink(), $comic->getName(), $comic->getDaily(), $comic->getImage());
        }
        $path = $this->getComicPath($name);
        file_put_contents($path, $html);
        $this->reportSuccess($width, strlen($message));
        $this->console->writeLine('Comic written to ' . $path);
    }

    public function allAction()
    {
        $this->verifyConsole();
        $supported = ComicFactory::getSupported();
        ksort($supported);

        $width  = $this->console->getWidth();
        $comics = array();
        foreach (array_keys($supported) as $alias) {
            $source  = ComicFactory::factory($alias);
            $message = "Fetching '$alias'";
            $this->console->write($message, Color::BLUE);
            try {
                $comic  = $source->fetch();
            } catch (\Exception $e) {
                $error = sprintf(
                    'Unable to fetch comic "%s"',
                    $alias
                );
                $this->reportError($width, strlen($message), $error, $e);
                continue;
            }
            if (!$comic) {
                $this->reportError($width, strlen($message), $source->getError());
                continue;
            }
            $this->reportSuccess($width, strlen($message));
            $comics[] = $comic;
        }

        $html     = '';
        $template =<<<EOT
<div class="comic">
    <h4><a href="%s">%s</a></h4>
    <p><a href="%s"><img src="%s"/></a></p>
</div>
EOT;

        $errTemplate =<<<EOT
<div class="comic">
    <h4><a href="%s">%s</a></h4>
    <p class="error">%s</p>
</div>
EOT;

        foreach ($comics as $comic) {
            if ($comic->hasError()) {
                $html .= sprintf($errTemplate . "\n", $comic->getLink(), $comic->getName(), $comic->getError());
                continue;
            }
            $html .= sprintf($template . "\n", $comic->getLink(), $comic->getName(), $comic->getDaily(), $comic->getImage());
        }
        $path = $this->getAllComicsPath();
        file_put_contents($path, $html);
        $this->console->writeLine('Comics written to ' . $path);
    }

    public function listAction()
    {
        $this->verifyConsole();

        $comics = ComicFactory::getSupported();
        ksort($comics);

        $mapped = array_map(function($name) {
            return strlen($name);
        }, array_keys($comics));
        $longest = array_reduce($mapped, function($count, $longest) {
            $longest = ($count > $longest) ? $count : $longest;
            return $longest;
        }, 0);

        $this->console->writeLine('Supported comics:', Color::GREEN);
        foreach ($comics as $alias => $info) {
            $this->console->writeLine(sprintf("    %${longest}s: %s", $alias, $info['name']));
        }
    }

    protected function verifyConsole()
    {
        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw RuntimeException(sprintf(
                '%s can only be run via the Console',
                __METHOD__
            ));
        }
    }

    protected function reportError($width, $length, $message, $e = null)
    {
        if (($length + 9) > $width) {
            $this->console->writeLine('');
            $length = 0;
        }
        $spaces = $width - $length - 9;
        $this->console->writeLine(str_repeat('.', $spaces) . '[ ERROR ]', Color::RED);
        $this->console->writeLine($message);
        if ($e) {
            $this->console->writeLine($e->getTraceAsString());
        }
    }

    protected function reportSuccess($width, $length)
    {
        if (($length + 8) > $width) {
            $this->console->writeLine('');
            $length = 0;
        }
        $spaces = $width - $length - 8;
        $this->console->writeLine(str_repeat('.', $spaces) . '[ DONE ]', Color::GREEN);
    }
}
