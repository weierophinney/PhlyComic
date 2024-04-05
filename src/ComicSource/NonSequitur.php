<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class NonSequitur extends GoComics
{
    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'nonsequitur',
            'Non Sequitur',
            'https://www.gocomics.com/nonsequitur'
        );
    }
}
