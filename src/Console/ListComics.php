<?php

declare(strict_types=1);

namespace PhlyComic\Console;

use PhlyComic\ComicFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function ksort;

class ListComics extends Command
{
    protected function configure()
    {
        $this->setName('list-comics');
        $this->setDescription('List all available comics');
        $this->setHelp(
            'Lists all comics that PhlyComic is capable of fetching,'
            . ' providing both the short name (used to fetch individual comics)'
            . ' and the full name.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Supported comics');

        $comics = (new ComicFactory())->getSupported();
        ksort($comics);

        $table = [];
        foreach ($comics as $comic) {
            $table[] = [$comic->name, $comic->title];
        }

        $io->table(
            ['Alias', 'Comic'],
            $table
        );

        return 0;
    }
}
