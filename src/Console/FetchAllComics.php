<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace PhlyComic\Console;

use PhlyComic\Comic;
use PhlyComic\ComicFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FetchAllComics extends Command
{
    use ComicConsoleTrait;

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
    }

    public function initialize(InputInterface $input, OutputInterface $output) : void
    {
        $io = new SymfonyStyle($input, $output);
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
    }

    /**
     * Fetch all comics and write to a file
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        if ($this->status > 0) {
            return $this->status;
        }

        $io = new SymfonyStyle($input, $output);
        $io->title('Fetching comics');

        $supported = ComicFactory::getSupported();
        ksort($supported);
        $supported = array_keys($supported);

        $exclude = $input->getOption('exclude');
        $toFetch = array_filter($supported, function ($comic) use ($exclude) {
            return ! in_array($comic, $exclude);
        });

        $html  = '';
        foreach ($toFetch as $alias) {
            $comic = $this->fetchComic($alias, $io);
            if (! $comic instanceof Comic) {
                continue;
            }

            if ($comic->hasError()) {
                $html .= sprintf($this->errorTemplate . "\n", $comic->getLink(), $comic->getName(), $comic->getError());
                continue;
            }

            $html .= sprintf($this->comicTemplate . "\n", $comic->getLink(), $comic->getName(), $comic->getDaily(), $comic->getImage());
        }

        $path = $input->getOption('output');
        file_put_contents($path, $html);

        $io->success(sprintf('Comics written to %s', $path));

        return 0;
    }
}
