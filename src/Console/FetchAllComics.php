<?php

declare(strict_types=1);

namespace PhlyComic\Console;

use PhlyComic\Comic;
use RuntimeException;
use Spatie\Async\Pool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function array_filter;
use function ctype_digit;
use function file_exists;
use function file_put_contents;
use function getcwd;
use function implode;
use function in_array;
use function is_int;
use function is_string;
use function ksort;
use function realpath;
use function sprintf;

use const SORT_NATURAL;

class FetchAllComics extends Command
{
    use ComicConsoleTrait;

    private int $processes = 0;

    protected function configure()
    {
        $this->setName('fetch-all');
        $this->setDescription('Fetch all comics');
        $this->setHelp(
            'Fetches all comics and writes an HTML file to the provided path;'
            . ' defaults to data/comics/comics.html. You may provide comics to exclude;'
            . ' the --exclude option allows multiple invocations, one for each comic'
            . ' you wish to exclude when fetching comics.'
        );

        $this->addOption(
            'output',
            'o',
            InputOption::VALUE_REQUIRED,
            'Path to which the HTML for the list of comic should be written',
            'data/comics/comics.html'
        );

        $this->addOption(
            'exclude',
            'e',
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Comic to exclude'
        );

        $this->addOption(
            'processes',
            'p',
            InputOption::VALUE_OPTIONAL,
            'Allow multiple parallel processes; specify an integer value for'
            . ' number of processes. Defaults to 5 if no value provided.',
            false
        );
    }

    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        $io           = new SymfonyStyle($input, $output);
        $this->status = 0;

        $outputPath = $input->getOption('output');
        if ($outputPath && ! $this->validateOutputValue($io, $outputPath)) {
            $this->status = 1;
        }

        foreach ($input->getOption('exclude') as $alias) {
            if (! $this->validateComicAlias($io, $alias)) {
                $this->status = 1;
            }
        }

        $processes = $input->getOption('processes');
        if ($processes !== false) {
            if (null === $processes) {
                $this->processes = 5;
            }

            if (is_int($processes)) {
                $this->processes = $processes;
            }

            if (is_string($processes) && ctype_digit($processes) && (int) $processes > -1) {
                $this->processes = (int) $processes;
            }
        }
    }

    /**
     * Fetch all comics and write to a file
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->status > 0) {
            return $this->status;
        }

        $io = new SymfonyStyle($input, $output);
        $io->title('Fetching comics');

        $supported = $this->getFactory()->getSupported();

        $exclude = $input->getOption('exclude');
        $toFetch = array_filter($supported, function (Comic $comic) use ($exclude) {
            return ! in_array($comic->name, $exclude);
        });

        $html = $this->processes > 0
            ? $this->fetchAsync($toFetch, $io)
            : $this->fetchSync($toFetch, $io);

        $path = $input->getOption('output');
        file_put_contents($path, $html);

        $io->success(sprintf('Comics written to %s', $path));

        return 0;
    }

    private function fetchSync(array $comics, SymfonyStyle $console): string
    {
        $html = '';
        foreach ($comics as $toFetch) {
            $comic = $this->fetchComic($toFetch->name, $console);
            if (! $comic instanceof Comic) {
                continue;
            }
            $html .= $this->createComicOutput($comic);
        }
        return $html;
    }

    private function fetchAsync(array $comics, SymfonyStyle $console): string
    {
        $pool = Pool::create()
            ->concurrency($this->processes)
            ->timeout(15)
            ->autoload($this->detectAutoloader())
            ->sleepTime(50000);

        $content = (object) ['comics' => []];
        foreach ($comics as $toFetch) {
            $console->text(sprintf('<info>Queuing retrieval of "%s"</info>', $toFetch->name));
            $pool
                ->add(function () use ($toFetch) {
                    $result = (object) [
                        'status' => null,
                        'comic'  => null,
                        'error'  => null,
                    ];
                    $source = $this->getFactory()->get($toFetch->name);
                    $comic  = $source->fetch($this->getHttpClient());

                    if ($comic->hasError()) {
                        $result->status = 1;
                        $result->error  = $comic->error;
                        return $result;
                    }

                    $result->status = 0;
                    $result->comic  = $comic;
                    return $result;
                })
                ->then(function ($result) use ($toFetch, $console, $content) {
                    if ($result->status !== 0) {
                        $this->reportError($console, sprintf(
                            'Error fetching %s: %s',
                            $toFetch->name,
                            $result->error
                        ));
                        return;
                    }

                    if (! $result->comic instanceof Comic) {
                        return;
                    }

                    $console->text(sprintf('<info>Completed retrieval of "%s</info>', $toFetch->name));
                    $content->comics[$toFetch->name] = $this->createComicOutput($result->comic);
                })
                ->catch(function (Throwable $e) use ($toFetch, $console) {
                    $this->reportError($console, sprintf(
                        'Error fetching %s: %s',
                        $toFetch->name,
                        $e->getMessage()
                    ));
                });
        }

        $pool->wait();
        ksort($content->comics, SORT_NATURAL);
        return implode("\n", $content->comics);
    }

    private function createComicOutput(Comic $comic): string
    {
        if ($comic->hasError()) {
            return sprintf(
                $this->errorTemplate . "\n",
                $comic->url,
                $comic->title,
                $comic->error,
            );
        }

        return sprintf(
            $this->comicTemplate . "\n",
            $comic->url,
            $comic->title,
            $comic->instanceUrl,
            $comic->instanceImageUrl,
        );
    }

    private function detectAutoloader(): string
    {
        $autoloaders = [
            realpath(getcwd()) . '/vendor/autoload.php',
            realpath(__DIR__) . '/../../vendor/autoload.php',
        ];

        foreach ($autoloaders as $autoloader) {
            if (file_exists($autoloader)) {
                return $autoloader;
            }
        }

        throw new RuntimeException(
            'Cannot detect autoloader; have you properly run `composer install`?'
        );
    }
}
