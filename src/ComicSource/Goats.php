<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class Goats extends GoComics
{
    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'goats',
            'Goats',
            'https://www.gocomics.com/goats'
        );
    }
}
