<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace PhlyComic\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FetchComic extends Command
{
    use ComicConsoleTrait;

    protected function configure()
    {
        $this->setName('fetch');
        $this->setDescription('Fetch a single comic');
        $this->setHelp(
            'Fetches the named <comic> and writes an HTML file to the provided path.'
        );

        $this->addArgument(
            'comic',
            InputArgument::REQUIRED,
            'Name (alias) of the comic to fetch'
        );

        $this->addOption(
            'output',
            'o',
            InputOption::VALUE_REQUIRED,
            'Path to which the HTML for the comic should be written'
        );
    }

    protected function initialize(InputInterface $input, OutputInterface $output) : void
    {
        $this->status = 0;
        $io = new SymfonyStyle($input, $output);

        if (! $this->validateComicAlias($io, $input->getArgument('comic') ?: '')) {
            $this->status = 1;
        }

        $outputPath = $input->getOption('output');
        if ($outputPath && ! $this->validateOutputValue($io, $outputPath ?: '')) {
            $this->status = 1;
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io = new SymfonyStyle($input, $output);
        if ($this->status > 0) {
            $io->error('One or more arguments or options are invalid.');
            return $this->status;
        }

        $name  = $input->getArgument('comic');
        $comic = $this->fetchComic($name, $io);
        if (! $comic) {
            return $this->status;
        }

        $io->text('<info>Generating HTML</>');
        $io->progressStart();

        if ($comic->hasError()) {
            $html = sprintf($this->errorTemplate . "\n", $comic->getLink(), $comic->getName(), $comic->getError());
        } else {
            $html = sprintf($this->comicTemplate . "\n", $comic->getLink(), $comic->getName(), $comic->getDaily(), $comic->getImage());
        }

        $path = $input->getOption('output') ?: sprintf('data/comics/%s.html', $name);
        file_put_contents($path, $html);
        $io->progressFinish();

        $io->success(sprintf('Comic written to %s', $path));

        return 0;
    }
}
